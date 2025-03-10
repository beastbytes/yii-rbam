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
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class UserController
{
    private const RBAM_ROLE = 'RbamUsersManager';

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
        name: RbamPermission::RbacUserView,
        parent: self::RBAM_ROLE
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
        name: RbamPermission::RbacUserView,
        parent: self::RBAM_ROLE
    )]
    public function view(CurrentRoute $currentRoute): ResponseInterface
    {
        $userId = $currentRoute
            ->getArgument('id')
        ;

        return $this
            ->viewRenderer
            ->render(
                'view',
                $this->getViewParameters($userId)
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function assignRole(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{name:string, item:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->assign($parsedBody['name'], $parsedBody['item'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_assignments',
                $this->getViewParameters($parsedBody['item'])
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function revokeAssignment(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{name:string, item:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->revoke($parsedBody['name'], $parsedBody['item'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_assignments',
                $this->getViewParameters($parsedBody['item'])
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function revokeAllAssignments(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{uid:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->revokeAll($parsedBody['uid'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_assignments',
                $this->getViewParameters($parsedBody['uid'])
            )
        ;
    }

    private function getViewParameters(string $userId): array
    {
        $user = $this
            ->userRepository
            ->findById($userId)
        ;
        $assignments = $this
            ->assignmentsStorage
            ->getByUserId($userId)
        ;
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

        $unassignedRoles = array_diff_key($roles, $assignedRoles);

        ksort($assignedRoles, SORT_STRING);
        ksort($unassignedRoles, SORT_STRING);

        return [
            'assignedRoles' => $assignedRoles,
            'assignments' => $assignments,
            'itemsStorage' => $this->itemsStorage,
            'permissionsGranted' => $permissionsGranted,
            'unassignedRoles' => $unassignedRoles,
            'user' => $user,
        ];
    }
}