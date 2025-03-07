<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Form\RuleForm;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamRuleInterface;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\RuleInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

use const DIRECTORY_SEPARATOR;

final class RuleController
{
    public function __construct(
        private readonly FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly Redirect $redirect,
        private readonly RuleServiceInterface $ruleService,
        private TranslatorInterface $translator,
        private ViewRenderer $viewRenderer
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

    #[PermissionAttribute(
        name: RbamPermission::RuleView,
        description: 'View Rule(s)',
        parent: RbamController::RBAM_ROLE
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

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'pageSize' => (int) ArrayHelper::getValue($queryParams, 'pagesize', 20),
                    'rules' => $rules
                ]
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RuleCreate,
        description: 'Create a Rule',
        parent: RbamController::RBAM_ROLE
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
                ->save($formModel)
            ;

            $this
                ->flash
                ->add(
                    'success',
                    $this
                        ->translator
                        ->translate(
                            'flash.rule-created',
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

    #[PermissionAttribute(
        name: RbamPermission::RuleDelete,
        description: 'Delete a Rule',
        parent: RbamController::RBAM_ROLE
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

    #[PermissionAttribute(
        name: RbamPermission::RuleUpdate,
        description: 'Update a Rule',
        parent: RbamController::RBAM_ROLE
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

        $previousName = $name;
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
                    ->save($formModel, $previousName)
            ) {
                $this
                    ->flash
                    ->add(
                        'success',
                        $this
                            ->translator
                            ->translate(
                                'flash.rule-updated',
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

    #[PermissionAttribute(
        name: RbamPermission::RuleView,
        description: 'View Rule(s)',
        parent: RbamController::RBAM_ROLE
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