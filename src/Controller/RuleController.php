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

        usort($rules, function(RbamRuleInterface $a, RbamRuleInterface $b) {
            return $a->getName() <=> $b->getName();
        });

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

    /**
     * Deletes authorisation rules
     *
     * The IDs of the rules to be deleted are contained in $_POST['ids']
     *
     * @return mixed The return value
    public function delete(): ResponseInterface
    {
        $am = Yii::$app->authManager;
        $success = $notDeleted = $notRemoved = [];

        foreach ($_POST['ids'] as $id) {
            if (!$am->remove($am->getRule($id))) {
                $notRemoved[] = $id;
            } else {
                if (unlink(str_replace('\\', '/', Yii::getAlias('@'.str_replace('\\', '/', $this->module->namespace))) . '/' . $id . '.php')) {
                    $success[] = $id;
                } else {
                    $notDeleted[] = $id;
                }
            }
        }

        if (!empty($success)) {
            Yii::$app->session->setFlash('success', [Yii::t('rbam', 'The following authorisation rules have been removed and their class files deleted: {success}', [
                'success' => Inflector::sentence($success, ' and ', ', and')
            ])]);
        }

        if (!empty($failure)) {
            Yii::$app->session->setFlash('failure', [Yii::t('rbam', 'The following authorisation rules have not been removed: {failure}', [
                'failure' => Inflector::sentence($notRemoved, ' and ', ', and')
            ])]);
        }

        if (!empty($failure)) {
            Yii::$app->session->setFlash('failure', [Yii::t('rbam', 'The following authorisation rules have been removed but their class files have not been deleted: {failure}', [
                'failure' => Inflector::sentence($notDeleted, ' and ', ', and')
            ])]);
        }

        return $this->redirect(['index']);
    }
     */

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