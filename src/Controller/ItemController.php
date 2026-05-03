<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\DTO\Assignment as RbamAssignment;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\DTO\PermittedUser;
use BeastBytes\Yii\Rbam\Form\ItemForm;
use BeastBytes\Yii\Rbam\ItemTypeService;
use BeastBytes\Yii\Rbam\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class ItemController
{
    private const RBAM_ROLE = 'rbam.item-manager';

    public function __construct(
        private readonly AssignmentsStorageInterface $assignmentsStorage,
        private readonly FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
        private readonly Redirect $redirect,
        private TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepositoryInterface $userRepository,
        private WebViewRenderer $viewRenderer
    )
    {
        $this->translator = $this
            ->translator
            ->withDefaultCategory('rbam')
        ;
        $this->viewRenderer = $this
            ->viewRenderer
            ->withViewPath('@views/item')
        ;
    }

    /**
     * List items - Roles or Permissions
     *
     * @param CurrentRoute $currentRoute
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemView,
        parent: self::RBAM_ROLE
    )]
    public function index(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $type = $this
            ->inflector
            ->toSingular(
                $currentRoute->getArgument('type')
            )
        ;

        if ($type === Item::TYPE_PERMISSION) {
            $items = $this
                ->itemsStorage
                ->getPermissions()
            ;
            uksort($items, fn(string $a, string $b) => $a <=> $b);
            array_walk($items, fn(Item &$item, $key, $ths)
                => $item = new RbamItem($item, $ths->getGrantedBy($item)), $this)
            ;
        } else {
            $items = $this
                ->itemsStorage
                ->getRoles()
            ;
            uksort($items, fn(string $a, string $b) => $a <=> $b);
            array_walk($items, fn(Item &$item) => $item = new RbamItem($item));
        }

        if ($request->getMethod() === Method::POST) {
            $parsedBody = $request->getParsedBody();

            return $this
                ->viewRenderer
                ->renderPartial(
                    '_items',
                    [
                        'actionButtons' => explode(',', $parsedBody['action_buttons']),
                        'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                        'noResultsText' => '',
                        'header' => $parsedBody['header'] ?? '',
                        'item' => null,
                        'items' => $items,
                        'paginationUrl' => $parsedBody['pagination_url'],
                        'toolbar' => $parsedBody['toolbar'] ?? '',
                        'type' => $type,
                        'user' => null,
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
                    'items' => $items,
                    'type' => $type,
                ],
            )
        ;
    }

    /**
     * Create a new item - Role or Permission
     *
     * @param CurrentRoute $currentRoute
     * @param FormHydrator $formHydrator
     * @param ServerRequestInterface $request
     * @param RuleServiceInterface $ruleService
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemCreate,
        parent: self::RBAM_ROLE
    )]
    public function create(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        ServerRequestInterface $request,
        RuleServiceInterface $ruleService,
    ): ResponseInterface
    {
        $type = $currentRoute->getArgument('type');

        $formModel = new ItemForm($this->translator);

        if ($formHydrator->populateFromPostAndValidate($formModel, $request)) {
            if ($type === Item::TYPE_PERMISSION) {
                $item = new Permission($formModel->getName());
            } else {
                $item = new Role($formModel->getName());
            }

            /** @psalm-suppress PossiblyNullArgument */
            $method = 'add' . ucfirst($type);
            $now = time();
            $this
                ->manager
                ->$method($item
                    ->withDescription($formModel->getDescription())
                    ->withRuleName($formModel->getRuleName())
                    ->withCreatedAt($now)
                    ->withUpdatedAt($now),
                )
            ;

            $this
                ->flash
                ->add(
                    'success',
                    $this
                        ->translator
                        ->translate(
                            'flash.item.created',
                            [
                                'name' => $formModel->getName(),
                                'type' => ucfirst($type),
                            ],
                        ),
                )
            ;

            return $this
                ->redirect
                ->toRoute(
                    'rbam.viewItem',
                    [
                        'name' => $this
                            ->inflector
                            ->toSnakeCase($item->getName()),
                        'type' => $type,
                    ],
                )
                ->withStatusCode(Status::SEE_OTHER)
                ->create()
            ;
        }

        return $this
            ->viewRenderer
            ->render(
                'itemForm',
                [
                    'formModel' => $formModel,
                    'ruleNames' => $ruleService->getRuleNames(),
                    'type' => $type,
                ],
            )
        ;
    }

    /**
     * Update an item - Role or Permission
     *
     * @param CurrentRoute $currentRoute
     * @param FormHydrator $formHydrator
     * @param NotFound $notFound
     * @param RuleServiceInterface $ruleService
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        RuleServiceInterface $ruleService,
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');

        if (!$this
            ->itemsStorage
            ->exists($name)
        ) {
            return $notFound->create();
        }

        $item = $this
            ->itemsStorage
            ->get($name)
        ;

        $type = ItemTypeService::getItemType($item);

        $formModel = new ItemForm($this->translator);

        if ($request->getMethod() === Method::POST) {
            if ($formHydrator->populateFromPostAndValidate($formModel, $request)) {
                /** @psalm-suppress PossiblyNullArgument */
                $method = 'update' . ucfirst($type);
                $item =  $item
                    ->withName($formModel->getName())
                    ->withDescription($formModel->getDescription())
                    ->withRuleName($formModel->getRuleName())
                    ->withUpdatedAt(time())
                ;

                $this
                    ->manager
                    ->$method($name, $item)
                ;

                $this
                    ->flash
                    ->add(
                        'success',
                        $this
                            ->translator
                            ->translate(
                                'flash.item.updated',
                                [
                                    'name' => $name,
                                    'type' => ucfirst($type),
                                ],
                            ),
                    )
                ;

                return $this
                    ->redirect
                    ->toRoute(
                        'rbam.viewItem',
                        [
                            'name' => $this
                                ->inflector
                                ->toSnakeCase($item->getName()),
                            'type' => $type,
                        ],
                    )
                    ->withStatusCode(Status::SEE_OTHER)
                    ->create()
                ;
            }
        } else {
            $formHydrator->populate(
                model: $formModel,
                data: [
                    'description' => $item->getDescription(),
                    'name' => $item->getName(),
                    'ruleName' => $item->getRuleName(),
                ],
                scope: '',
            );
        }

        return $this
            ->viewRenderer
            ->render(
                'itemForm',
                [
                    'formModel' => $formModel,
                    'type' => $type,
                    'ruleNames' => $ruleService->getRuleNames(),
                ],
            )
        ;
    }

    /**
     * View an item - Role or Permission
     *
     * @param CurrentRoute $currentRoute
     * @param NotFound $notFound
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemView,
        parent: self::RBAM_ROLE
    )]
    public function view(CurrentRoute $currentRoute, NotFound $notFound): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');

        if (!$this
            ->itemsStorage
            ->exists($name)
        ) {
            return $notFound->create();
        }

        $item = $this
            ->itemsStorage
            ->get($name)
        ;

        return $item->getType() === Item::TYPE_PERMISSION
            ? $this->viewPermission($item)
            : $this->viewRole($item)
        ;
    }

    private function viewPermission(Permission $item): ResponseInterface
    {
        $permittedUsers = $this->getPermittedUsers($item);

        return $this
            ->viewRenderer
            ->render(
                'viewPermission',
                [
                    'diagram' => (new MermaidHierarchyDiagram(
                        $this->itemsStorage,
                        $this->translator,
                        $this->urlGenerator
                    ))
                        ->withItem($item)
                    ,
                    'item' => new RbamItem($item, $this->getGrantedBy($item)),
                    'permittedUsers' => $permittedUsers,
                ]
            )
        ;
    }

    private function viewRole(Role $item): ResponseInterface
    {
        $name = $item->getName();

        $ancestors = $this
            ->itemsStorage
            ->getParents($name)
        ;

        $assignments = [];

        $rbacAssignments = $this
            ->assignmentsStorage
            ->getByItemNames([$name])
        ;

        foreach ($this
            ->userRepository
            ->findByIds(
                $this
                    ->manager
                    ->getUserIdsByRoleName($name)
            )
        as $user) {
            $assignment = array_find(
                $rbacAssignments,
                fn(Assignment $assignment) => $assignment->getUserId() === $user->getId()
            );

            $assignments[] = new RbamAssignment(
                $user,
                $assignment instanceof Assignment // direct assignment
                    ? $item
                    : array_first($ancestors) // inherited assignment
            );
        }

        $permissions = $this
            ->manager
            ->getPermissionsByRoleName($name)
        ;
        uksort($permissions, fn(string $a, string $b) => $a <=> $b);
        array_walk(
            $permissions,
            fn(Item &$item, $key, $ths) => $item = new RbamItem($item, $ths->getGrantedBy($item)), $this
        );

        $roles = $this
            ->manager
            ->getChildRoles($name)
        ;
        uksort($roles, fn(string $a, string $b) => $a <=> $b);
        array_walk($roles, fn(Item &$item) => $item = new RbamItem($item));

        return $this
            ->viewRenderer
            ->render(
                'viewRole',
                [
                    'assignments' => $assignments,
                    'diagram' => (new MermaidHierarchyDiagram(
                        $this->itemsStorage,
                        $this->translator,
                        $this->urlGenerator
                    ))
                        ->withItem($item)
                    ,
                    'item' => $item,
                    'permissions' => $permissions,
                    'roles' => $roles,
                ]
            )
        ;
    }

    // -------- POST only methods -------- //

    /**
     * Add a child item - Role or Permission - to a Role
     *
     * Creates a parent/child relationship
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function addChild(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->addChild($parsedBody['item'], $parsedBody['name'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_children',
                $this->getViewParameters($parsedBody['item'], $parsedBody['type']),
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::itemView,
        parent: self::RBAM_ROLE
    )]
    public function assignments(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{name:string, id:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        $item = $this
            ->manager
            ->getRole($parsedBody['name'])
        ;

        $ancestors = $this
            ->itemsStorage
            ->getParents($parsedBody['name'])
        ;

        $assignments = [];
        $rbacAssignments = $this->assignmentsStorage->getByItemNames([$parsedBody['name']]);

        foreach ($this->userRepository
            ->findByIds(
                $this
                    ->manager
                    ->getUserIdsByRoleName($parsedBody['name']),
            )
        as $user) {
            $assignment = array_find(
                $rbacAssignments,
                fn(Assignment $assignment) => $assignment->getUserId() === $user->getId()
            );

            $assignments[] = new RbamAssignment(
                $user,
                $assignment instanceof Assignment // direct assignment
                    ? $item
                    : array_first($ancestors) // inherited assignment
            );
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_assignments',
                [
                    'assignments' => $assignments,
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'item' => $item,
                ]
            )
        ;
    }

    /**
     * Manage children - child Roles or Permissions - of a Role
     *
     * @param CurrentRoute $currentRoute
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemView,
        parent: self::RBAM_ROLE
    )]
    public function children(CurrentRoute $currentRoute): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render(
                'children',
                $this->getViewParameters(
                    $currentRoute->getArgument('name'),
                    $currentRoute->getArgument('childType')
                ),
            )
        ;
    }

    /**
     * Paginator for child roles
     *
     * POST
     */
    public function childItems(CurrentRoute $currentRoute, ServerRequestInterface $request): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');
        /** @var array{name:string, id:string} $parsedBody */
        $parsedBody = $request->getParsedBody();
        $type = $parsedBody['type'];

        if ($type === Item::TYPE_PERMISSION) {
            $items = $this
                ->manager
                ->getPermissionsByRoleName($name)
            ;
            uksort($items, fn(string $a, string $b) => $a <=> $b);
            array_walk($items, fn(Item &$item, $key, $ths)
                => $item = new RbamItem($item, $ths->getGrantedBy($item)), $this)
            ;
        } else {
            $items = $this
                ->manager
                ->getChildRoles($name)
            ;
            uksort($items, fn(string $a, string $b) => $a <=> $b);
            array_walk($items, fn(Item &$item) => $item = new RbamItem($item));
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_items',
                [
                    'actionButtons' => explode(',', $parsedBody['action_buttons']),
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'noResultsText' => '',
                    'header' => $parsedBody['header'] ?? '',
                    'item' => $this
                        ->itemsStorage
                        ->get($name)
                    ,
                    'items' => $items,
                    'paginationUrl' => $parsedBody['pagination_url'],
                    'toolbar' => $parsedBody['toolbar'],
                    'type' => $type,
                ]
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::itemView,
        parent: self::RBAM_ROLE
    )]
    public function permittedUsers(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $permission = $this->itemsStorage->getPermission($currentRoute->getArgument('name'));

        return $this
            ->viewRenderer
            ->renderPartial(
                '_permittedUsers',
                [
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'permission' => $permission,
                    'permittedUsers' => $this->getPermittedUsers($permission),
                ]
            )
        ;
    }

    /**
     * Remove - delete - an item - Role or Permission
     *
     * If the item has child items the parent/child relationships are deleted, but the child items are not
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @psalm-suppress PossiblyNullArgument
     */
    #[PermissionAttribute(
        name: RbamPermission::itemRemove,
        parent: self::RBAM_ROLE
    )]
    public function remove(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $type = ItemTypeService::getItemType($this
            ->itemsStorage
            ->get($parsedBody['item']),
        );

        foreach ($this->itemsStorage->getRoles() as $role) {
            if ($this->itemsStorage->hasChild($parsedBody['item'], $role->getName())) {
                $this->itemsStorage->removeChild($parsedBody['item'], $role->getName());
            }
        }

        $method = 'remove' . ucfirst($type);
        $this
            ->manager
            ->$method($parsedBody['item'])
        ;

        $items = $this->getViewParameters($parsedBody['item'], $parsedBody['type'])['items'];

        foreach ($items as $i => $item) {
            $items[$i] = new \BeastBytes\Yii\Rbam\DTO\Item($item);
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_items',
                [
                    'actionButtons' => explode(',', $parsedBody['action_buttons']),
                    'currentPage' => 1,
                    'header' => $parsedBody['header'] ?? '',
                    'item' => null,
                    'items' => $items,
                    'noResultsText' => '',
                    'paginationUrl' => $parsedBody['pagination_url'],
                    'toolbar' => $parsedBody['toolbar'] ?? '',
                    'type' => $type,
                    'user' => null,
                ]
            )
            ;
    }

    /**
     * Remove all child items - Roles or Permissions - from a Role
     *
     * The child items are not deleted, only the parent/child relationships
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function removeAllChildren(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->removeChildren($parsedBody['item'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_children',
                $this->getViewParameters($parsedBody['item'], $parsedBody['type']),
            )
        ;
    }

    /**
     * Remove a child item - Role or Permission - from a Role
     *
     * The child item is not deleted, only the parent/child relationship
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::itemUpdate,
        parent: self::RBAM_ROLE
    )]
    public function removeChild(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->removeChild($parsedBody['item'], $parsedBody['name'])
        ;

        return $this
            ->viewRenderer
            ->renderPartial(
                '_children',
                $this->getViewParameters($parsedBody['item'], $parsedBody['type']),
            )
        ;
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

    private function getPermittedUsers(Permission $permission): array
    {
        $ancestors = $this
            ->itemsStorage
            ->getParents($permission->getName())
        ;
        $permittedUsers = [];
        $userIds = [];

        foreach ($ancestors as $ancestor) {
            if (
                $ancestor->getType() === Item::TYPE_ROLE
                && $this->manager->hasChild($ancestor->getName(), $permission->getName())
            ) {
                $ids = $this
                    ->manager
                    ->getUserIdsByRoleName($ancestor->getName())
                ;
                array_push($userIds, ...$ids);
            }
        }

        $users = $this
            ->userRepository
            ->findByIds(array_unique($userIds, SORT_REGULAR))
        ;

        foreach ($users as $user) {
            foreach ($ancestors as $ancestor) {
                $assignment = $this
                    ->assignmentsStorage
                    ->get($ancestor->getName(), $user->getId())
                ;
                if ($assignment !== null) {
                    $permittedUsers[] = new PermittedUser($user, $ancestor, $assignment);
                }
            }
        }

        return $permittedUsers;
    }

    private function getViewParameters(string $name, string $type): array
    {
        $children = array_keys($this
            ->itemsStorage
            ->getDirectChildren($name),
        );

        if ($type === Item::TYPE_PERMISSION) {
            $descendants = $this
                ->itemsStorage
                ->getAllChildPermissions($name)
            ;
            $items = $this
                ->itemsStorage
                ->getPermissions()
            ;
        } else {
            $descendants = $this
                ->itemsStorage
                ->getAllChildRoles($name)
            ;
            $items = $this
                ->itemsStorage
                ->getRoles()
            ;

            unset($items[$name]);
        }

        $items = array_diff_key($items, $descendants);

        ksort($descendants);
        ksort($items);

        $ancestors = $this
            ->itemsStorage
            ->getParents($name)
        ;

        $parent = $this
            ->itemsStorage
            ->getRole($name)
        ;

        $ancestors[] = $parent;

        return [
            'ancestors' => $ancestors,
            'children' => $children,
            'descendants' => $descendants,
            'items' => $items,
            'manager' => $this->manager,
            'parent' => $parent,
            'type' => $type,
        ];
    }
}