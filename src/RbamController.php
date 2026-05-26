<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use BeastBytes\Yii\Rbam\Rule\RuleServiceInterface;
use BeastBytes\Yii\Rbam\User\UserRepositoryInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Files\FileHelper;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Role;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

#[RoleAttribute(RbamRole::admin)]
final class RbamController
{
    private const string CODE = 'bZc148QoubK0WjFJYngQwda';

    public function __construct(
        private readonly AssignmentsStorageInterface $assignmentsStorage,
        private readonly InitialisationService $initialisationService,
        private readonly ItemsStorageInterface $itemsStorage,
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
     * Display the RBAM dashboard
     *
     * @param RuleServiceInterface $ruleService
     * @param UserRepositoryInterface $userRepository
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        RbamPermission::index,
        parent: [RbamRole::itemManager, RbamRole::ruleManager, RbamRole::userManager]
    )]
    public function index(
        CurrentUser $currentUser,
        RuleServiceInterface $ruleService,
        UserRepositoryInterface $userRepository,
    ): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentUser' => $currentUser,
                    'permissions' => count($this
                        ->itemsStorage
                        ->getPermissions()
                    ),
                    'roles' => count($this
                        ->itemsStorage
                        ->getRoles()
                    ),
                    'rules' => count($ruleService->getRules()),
                    'users' => $userRepository->count()
                ]
            )
        ;
    }

    #[PermissionAttribute(RbamPermission::clear)]
    public function clear(
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request,
        SessionInterface $session,
    ): ResponseInterface
    {
        $formModel = new ClearForm($this->translator, self::CODE);

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            $this->assignmentsStorage->clear();
            $this->itemsStorage->clear();

            return $redirect
                ->toRoute('rbam.initialise')
                ->create()
            ;
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_clearForm',
                [
                    'formModel' => $formModel
                ]
            )
        ;
    }

    public function initialise(
        FlashInterface $flash,
        FormHydrator $formHydrator,
        ManagerInterface $manager,
        RbamParameters $parameters,
        Redirect $redirect,
        ServerRequest $request
    ): ResponseInterface
    {
        if (
            count($this->itemsStorage->getPermissions()) > 0
            || count($this->itemsStorage->getRoles()) > 0
        ) {
            $flash
                ->add(
                    'info',
                    $this
                        ->translator
                        ->translate('flash.rbac.already-initialised'),
                )
            ;

            return $redirect
                ->toRoute('rbam.rbam')
                ->create()
            ;
        }

        $formModel = new InitialiseForm($this->translator);

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            $rbamFiles = FileHelper::findFiles(
                dirname(__DIR__),
                [
                    'filter' => (new PathMatcher())
                        ->only('**Controller.php'),
                    'recursive' => true,
                ]
            );

            if ($formModel->shouldInitialiseApplication()) {
                $applicationFiles = FileHelper::findFiles(
                    $formModel->getSrcDir(),
                    [
                        'filter' => (new PathMatcher())
                            ->except($formModel->getExcept())
                            ->only($formModel->getOnly()),
                        'recursive' => true,
                    ]
                );
            } else {
                $applicationFiles = [];
            }

            foreach (array_merge($rbamFiles, $applicationFiles) as $file) {
                $this->initialisationService->processFile($file);
            }

            if (!empty($manager->getDefaultRoleNames())) {
                foreach ($manager->getDefaultRoleNames() as $i => $roleName) {
                    $manager->addRole(
                        (new Role($roleName))
                            ->withDescription($parameters->getDefaultRoles()[$i]['description'])
                    );
                }
            }

            if (is_string($manager->getGuestRoleName())) {
                $manager->addRole(
                    (new Role($manager->getGuestRoleName()))
                        ->withDescription($parameters->getGuestRole()['description'])
                );
            }

            $userIds = explode(',', $formModel->getuserId());
            array_walk($userIds, fn(string &$userId) => $userId = trim($userId));
            foreach ($userIds as $userId) {
                $manager->assign(RbamRole::admin->getItemName(), $userId);
            }

            if ($this->initialisationService->hasErrors()) {
                foreach ($this->initialisationService->getErrors() as $error) {
                    $flash
                        ->add(
                            'error',
                            $this
                                ->translator
                                ->translate($error),
                        )
                    ;
                }
            } else {
                $flash
                    ->add(
                        'success',
                        $this
                            ->translator
                            ->translate('flash.rbac.initialised'),
                    )
                ;
            }

            return $redirect
                ->toRoute('rbam.rbam')
                ->create()
            ;
        }

        return $this
            ->viewRenderer
            ->render('initialiseForm', ['formModel' => $formModel])
        ;
    }
}