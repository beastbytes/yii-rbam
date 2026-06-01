<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use BeastBytes\Yii\Rbam\TranslationService;
use BeastBytes\Yii\Rbam\TranslationServiceInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

#[RoleAttribute(name: RbamRole::ruleManager, parent: RbamRole::admin)]
final class RuleController
{
    public function __construct(
        private readonly FlashInterface $flash,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly Redirect $redirect,
        private readonly RuleServiceInterface $ruleService,
        private TranslatorInterface $translator,
        private WebViewRenderer $viewRenderer
    ) {
        $this->translator = $this
            ->translator
            ->withDefaultCategory('rbam');
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this);
    }

    /**
     * List rules
     *
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::ruleView)]
    public function index(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams();
        $rules = $this
            ->ruleService
            ->getRules();

        ksort($rules, SORT_STRING);

        if ($request->getMethod() === Method::POST) {
            return $this
                ->viewRenderer
                ->renderPartial(
                    'index',
                    [
                        'currentPage' => (int)ArrayHelper::getValue($queryParams, 'page', 1),
                        'rules' => $rules,
                    ]
                );
        }

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int)ArrayHelper::getValue($queryParams, 'page', 1),
                    'rules' => $rules,
                ]
            );
    }

    /**
     * Create a rule
     *
     * @param FormHydrator $formHydrator
     * @param Redirect $redirect
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::ruleCreate)]
    public function create(
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $formModel = new CreateRuleForm($this->translator, $this->ruleService);

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            $this
                ->ruleService
                ->save($formModel->getName(), $formModel->getDescription(), $formModel->getCode());

            $this
                ->flash
                ->add(
                    'success',
                    $this
                        ->translator
                        ->translate(
                            'flash.rule.created',
                            [
                                'name' => $formModel->getName(),
                            ]
                        )
                );

            return $redirect
                ->toRoute(
                    'rbam.rule.view',
                    [
                        'name' => $formModel->getName(),
                    ]
                )
                ->withStatusCode(Status::SEE_OTHER)
                ->create();
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel]);
    }

    /**
     * Delete a rule
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::ruleDelete)]
    public function delete(
        ServerRequestInterface $request,
        TranslationService $translationService,
    ): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        foreach ($this->itemsStorage->getPermissions() as $permission) {
            if ($permission->getRuleName() === $parsedBody['rule']) {
                $this->itemsStorage->update(
                    $permission->getName(),
                    $permission->withRuleName(null)
                );
            }
        }

        foreach ($this->itemsStorage->getRoles() as $role) {
            if ($role->getRuleName() === $parsedBody['rule']) {
                $this->itemsStorage->update(
                    $role->getName(),
                    $role->withRuleName(null)
                );
            }
        }

        $this->ruleService->delete($parsedBody['rule']);
        $translationService->deleteRule($parsedBody['rule']);

        return $this
            ->viewRenderer
            ->renderPartial(
                'index',
                [
                    'rules' => $this
                        ->ruleService
                        ->getRules()
                ]
            );
    }

    #[PermissionAttribute(RbamPermission::ruleUpdate)]
    public function translate(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        ServerRequestInterface $request,
        TranslationServiceInterface $translationService,
    ): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');
        $rule = $this->ruleService->getRule($name);

        if ($rule === null) {
            return $notFound->create();
        }

        $formModel = new TranslationForm();

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            $translations = [];

            foreach ($formModel->getTranslations() as $translation) {
                $translations[$translation->getLocale()]['rbac-rule'][$rule->getDescription()]
                    = $translation->getDescription()
                ;
            }

            $translationService->save($translations);

            return $this
                ->redirect
                ->toRoute('rbam.rule.view', ['name' => $name])
                ->create()
            ;
        }

        if (!$formModel->hasTranslations()) {
            $formModel = $formModel->withTranslations(
                $translationService->getRuleTranslations($rule)
            );
        }

        return $this
            ->viewRenderer
            ->render(
                'translationForm',
                [
                    'formModel' => $formModel,
                    'rule' => $rule,
                ]
            )
        ;
    }

    /**
     * Update a rule
     *
     * @param CurrentRoute $currentRoute
     * @param FormHydrator $formHydrator
     * @param NotFound $notFound
     * @param Redirect $redirect
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::ruleUpdate)]
    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        Redirect $redirect,
        ServerRequestInterface $request,
        TranslationServiceInterface $translationService,
    ): ResponseInterface {
        $name = $currentRoute->getArgument('name');

        $rule = $this
            ->ruleService
            ->getRule($name);

        if ($rule === null) {
            return $notFound->create();
        }

        $formModel = new UpdateRuleForm($this->translator);
        $formHydrator->populate(
            model: $formModel,
            data: [
                'code' => trim($rule->getCode()),
                'description' => $rule->getDescription(),
                'name' => $rule->getName(),
            ],
            scope: ''
        );

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            if (
                $this
                    ->ruleService
                    ->save($formModel->getName(), $formModel->getDescription(), $formModel->getCode())
            ) {

                $translationService
                    ->updateRule($rule->getDescription(), $formModel->getDescription())
                ;

                $this
                    ->flash
                    ->add(
                        'success',
                        $this
                            ->translator
                            ->translate(
                                'flash.rule.updated',
                                [
                                    'name' => $name,
                                ]
                            )
                    );

                return $redirect
                    ->toRoute('rbam.rule.view', ['name' => $name])
                    ->withStatusCode(Status::SEE_OTHER)
                    ->create();
            }
            // @todo What if rule wasn't saved
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel]);
    }

    /**
     * View a rule
     *
     * @param CurrentRoute $currentRoute
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::ruleView)]
    public function view(
        CurrentRoute $currentRoute,
        NotFound $notFound,
    ): ResponseInterface {
        $name = $currentRoute->getArgument('name');

        $rule = $this
            ->ruleService
            ->getRule($name);

        if ($rule === null) {
            return $notFound
                ->create();
        }

        return $this
            ->viewRenderer
            ->render(
                'view',
                [
                    'permissions' => $this->getItems(Item::TYPE_PERMISSION, $name),
                    'roles' => $this->getItems(Item::TYPE_ROLE, $name),
                    'rule' => $rule,
                ]
            );
    }

    /**
     * Permission and role pagination
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function items(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        return $this
            ->viewRenderer
            ->renderPartial(
                '_items',
                [
                    'actionButtons' => explode(',', $parsedBody['action_buttons']),
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'noResultsText' => '',
                    'header' => $parsedBody['header'] ?? '',
                    'item' => null,
                    'items' => $this->getItems($parsedBody['type'], $parsedBody['name']),
                    'paginationUrl' => $parsedBody['pagination_url'],
                    'toolbar' => '',
                    'type' => $parsedBody['type'],
                ]
            );
    }

    private function getItems(string $type, string $name): array
    {
        $items = [];

        $method = 'get' . ucfirst($type) . 's';

        /** @var Item $item */
        foreach ($this->itemsStorage->$method() as $item) {
            $rule = is_string($item->getRuleName()) ? substr($item->getRuleName(), 30, -4) : null;

            if ($rule === $name) {
                if ($type === Item::TYPE_PERMISSION) {
                    /** @var Permission $item */
                    $items[$item->getName()] = (new RbamItem($item))->withParents($this->getGrantedBy($item));
                } else {
                    $items[$item->getName()] = new RbamItem($item);
                }
            }
        }

        ksort($items);

        return $items;
    }

    private function getGrantedBy(Permission $permission): array
    {
        $parents = [];

        foreach ($this->itemsStorage->getParents($permission->getName()) as $ancestor) {
            if ($this->itemsStorage->hasDirectChild($ancestor->getName(), $permission->getName())) {
                $parents[] = $ancestor;
            }
        }

        return $parents;
    }
}