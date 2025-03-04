<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

use const DIRECTORY_SEPARATOR;

class UserController
{
    public function __construct(
        private readonly AssignmentsStorageInterface $assignmentsStorage,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
        private readonly UserRepositoryInterface $userRepository,
        private ViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this)
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::UserView,
        description: 'View user(s)',
        parent: RbamController::RBAM_ROLE
    )]
    public function index(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $users = $this
            ->userRepository
            ->findAll()
        ;

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'pageSize' => (int) ArrayHelper::getValue($queryParams, 'pagesize', 20),
                    'users' => $users
                ]
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::UserView,
        description: 'View user(s)',
        parent: RbamController::RBAM_ROLE
    )]
    public function view(
        CurrentRoute $currentRoute,
        UserRepositoryInterface $userRepository
    ): ResponseInterface
    {
        $userId = $currentRoute
            ->getArgument('id')
        ;

        $user = $userRepository->findById($userId);
        $assignments = $this->assignmentsStorage->getByUserId($userId);
        $assignedRoles = $this
            ->manager
            ->getRolesByUserId($userId)
        ;
        $permissionsGranted = $this
            ->manager
            ->getPermissionsByUserId($userId)
        ;
        $roles = $this
            ->itemsStorage
            ->getRoles()
        ;

        return $this
            ->viewRenderer
            ->render(
                'view',
                [
                    'assignedRoles' => $assignedRoles,
                    'assignments' => $assignments,
                    'itemsStorage' => $this->itemsStorage,
                    'permissionsGranted' => $permissionsGranted,
                    'roles' =>  $roles,
                    'user' => $user
                ]
            )
        ;
    }
}