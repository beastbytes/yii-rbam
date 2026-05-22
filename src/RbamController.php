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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

#[RoleAttribute(RbamRole::admin)]
#[RoleAttribute(RbamRole::itemManager)]
#[RoleAttribute(RbamRole::ruleManager)]
#[RoleAttribute(RbamRole::userManager)]
final class RbamController
{
    public function __construct(
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
    #[PermissionAttribute(RbamPermission::index)]
    public function index(
        CurrentUser $currentUser,
        Redirect $redirect,
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
}