<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Form\RuleForm;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class RuleController
{
    private const RBAM_ROLE = 'rbam.rule-manager';

    public function __construct(
        private readonly FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly Redirect $redirect,
        private readonly RuleServiceInterface $ruleService,
        private TranslatorInterface $translator,
        private WebViewRenderer $viewRenderer
    )
    {
        $this->translator = $this
            ->translator
            ->withDefaultCategory('rbam')
        ;
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this)
        ;
    }

    /**
     * List rules
     *
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::ruleView,
        parent: self::RBAM_ROLE
    )]
    public function index(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $rules = $this
            ->ruleService
            ->getRules()
        ;

        ksort($rules, SORT_STRING);

        if ($request->hasHeader('Yii-Request')) {
            return $this
                ->viewRenderer
                ->renderPartial(
                    'index',
                    [
                        'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                        'rules' => $rules,
                    ]
                )
            ;
        }

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'rules' => $rules,
                ]
            )
        ;
    }

    /**
     * Create a rule
     *
     * @param FormHydrator $formHydrator
     * @param Redirect $redirect
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::ruleCreate,
        parent: self::RBAM_ROLE
    )]
    public function create(
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $formModel = new RuleForm($this->translator);

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populate($formModel, $request->getParsedBody())
            && $formModel->isValid()
        ) {
            $this
                ->ruleService
                ->save($formModel->getName(), $formModel->getDescription(), $formModel->getCode())
            ;

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
                )
            ;

            return $redirect
                ->toRoute(
                    'rbam.viewRule',
                    [
                        'name' => $this
                            ->inflector
                            ->toSnakeCase($formModel->getName()),
                    ]
                )
                ->withStatusCode(Status::SEE_OTHER)
                ->create()
            ;
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel])
        ;
    }

    /**
     * Delete a rule
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::ruleDelete,
        parent: self::RBAM_ROLE
    )]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        foreach ($this->itemsStorage->getPermissions() as $permission) {
            if ($permission->getRuleName() === $parsedBody['name']) {
                $this->itemsStorage->update(
                    $permission->getName(),
                    $permission->withRuleName(null)
                );
            }
        }

        foreach ($this->itemsStorage->getRoles() as $role) {
            if ($role->getRuleName() === $parsedBody['name']) {
                $this->itemsStorage->update(
                    $role->getName(),
                    $role->withRuleName(null)
                );
            }
        }

        $this->ruleService->delete($parsedBody['name']);

        return $this
            ->redirect
            ->toRoute('rbam.ruleIndex')
            ->create()
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
    #[PermissionAttribute(
        name: RbamPermission::ruleUpdate,
        parent: self::RBAM_ROLE
    )]
    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        Redirect $redirect,
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

        $rule = $this
            ->ruleService
            ->getRule($name)
        ;

        if ($rule === null) {
            return $notFound->create();
        }

        $formModel = new RuleForm($this->translator);
        $formHydrator->populate(
            model: $formModel,
            data: [
                'code' => $rule->getCode(),
                'description' => $rule->getDescription(),
                'name' => $rule->getName(),
            ],
            scope: ''
        );

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populateFromPostAndValidate($formModel, $request)
        ) {
            if (
                $this
                    ->ruleService
                    ->save($formModel->getName(), $formModel->getDescription(), $formModel->getCode())
            ) {
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
                    )
                ;

                return $redirect
                    ->toRoute('rbam.viewRule', ['name' => $this->inflector->toSnakeCase($name)])
                    ->withStatusCode(Status::SEE_OTHER)
                    ->create()
                ;
            }

            // @todo What if rule wasn't saved
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel])
        ;
    }

    /**
     * View a rule
     *
     * @param CurrentRoute $currentRoute
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::ruleView,
        parent: self::RBAM_ROLE
    )]
    public function view(
        CurrentRoute $currentRoute
    ): ResponseInterface
    {
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

        $rule = $this
            ->ruleService
            ->getRule($name)
        ;

        //$usedBy = Yii::$app->authManager->getItemsByRule($name);
        return $this
            ->viewRenderer
            ->render('view', [
                'rule' => $rule,
            ]);
    }
}