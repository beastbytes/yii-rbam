<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\User;

use BeastBytes\Yii\Rbam\DTO\Item;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Http\Method;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

#[RoleAttribute(name: RbamRole::userManager, parent: RbamRole::admin)]
final class UserController
{
    public function __construct(
        private readonly AssignmentsStorageInterface $assignmentsStorage,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
        private readonly UserRepositoryInterface $userRepository,
        private WebViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this)
        ;
    }

    /**
     * List users
     *
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::userView)]
    public function index(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $users = $this
            ->userRepository
            ->findAll()
        ;

        if ($request->getMethod() === Method::POST) {
            return $this
                ->viewRenderer
                ->renderPartial(
                    'index',
                    [
                        'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                        'users' => $users
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
                    'users' => $users
                ]
            )
        ;
    }

    /**
     * Show the Roles assigned to a user and the Permissions the grant, also list unassigned Roles
     *
     * @param CurrentRoute $currentRoute
     * @return ResponseInterface
     */
    #[PermissionAttribute(RbamPermission::userView)]
    public function view(CurrentRoute $currentRoute): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render(
                'view',
                $this->getViewParameters($currentRoute->getArgument('id'))
            )
        ;
    }

    /**
     * Assign a Role to a user
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function assign(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{name:string, item:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->assign($parsedBody['item'], $parsedBody['user'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial('view', $this->getViewParameters($parsedBody['user']))
        ;
    }

    /**
     * Revoke Role assignment(s) from a user
     *
     * If a role name is not specified, all assignments are revoked
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function revoke(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{item:string, user:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        if (isset($parsedBody['item'])) {
            $this
                ->manager
                ->revoke($parsedBody['item'], $parsedBody['user'])
            ;
        } else {
            $this
                ->manager
                ->revokeAll($parsedBody['user'])
            ;
        }

        return $this
            ->viewRenderer
            ->renderPartial('view', $this->getViewParameters($parsedBody['user']))
        ;
    }

    /**
     * Page Permissions granted to a user
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function permissions(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();

        $permissions = $this
            ->manager
            ->getPermissionsByUserId($parsedBody['user'])
        ;

        ksort($permissions, SORT_STRING);
        array_walk(
            $permissions,
            fn(\Yiisoft\Rbac\Item &$item, $key, $ths) => $item
                = (new Item($item))->withParents($ths->getGrantedBy($item)), $this
        );

        return $this
            ->viewRenderer
            ->renderPartial(
                '//item/_items',
                [
                    'actionButtons' => ['view'],
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'header' => 'label.permissions.granted',
                    'item' => null,
                    'items' => $permissions,
                    'noResultsText' => 'message.no-permissions-granted',
                    'paginationUrl' => $parsedBody['pagination_url'],
                    'toolbar' => '',
                    'type' => 'permission',
                    'user' => $this
                        ->userRepository
                        ->findById($parsedBody['user']),
                ]
            )
        ;
    }

    /**
     * Page Roles assigned or unassigned to a user
     *
     * POST
     *
     * @param CurrentRoute $currentRoute
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function roles(CurrentRoute $currentRoute, ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();

        return $this
            ->viewRenderer
            ->renderPartial(
                '_' . $currentRoute->getArgument('status') . 'Roles',
                array_merge(
                    $this->getViewParameters($parsedBody['user']),
                    [
                        'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    ]
                )
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
        array_walk($assignedRoles, fn(Role &$item) => $item = new Item($item));
        ksort($unassignedRoles, SORT_STRING);
        ksort($permissionsGranted, SORT_STRING);
        array_walk(
            $permissionsGranted,
            fn(Permission &$item, $key, $ths) => $item
                = (new Item($item))->withParents($ths->getGrantedBy($item)), $this
        );

        return [
            'assignedRoles' => $assignedRoles,
            'assignments' => $assignments,
            'permissionsGranted' => $permissionsGranted,
            'unassignedRoles' => $unassignedRoles,
            'user' => $user,
        ];
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