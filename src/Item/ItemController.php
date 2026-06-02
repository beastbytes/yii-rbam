<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Item;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\DTO\Assignment as RbamAssignment;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\DTO\User as RbamUser;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use BeastBytes\Yii\Rbam\Rule\RuleServiceInterface;
use BeastBytes\Yii\Rbam\TranslationServiceInterface;
use BeastBytes\Yii\Rbam\User\UserRepositoryInterface;
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

#[RoleAttribute(item: RbamRole::itemManager, parent: RbamRole::admin)]
final class ItemController
{
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
    #[PermissionAttribute(item: RbamPermission::itemView)]
    public function index(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $filter = ArrayHelper::getValue($request->getQueryParams(), 'filter');

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
        } else {
            $items = $this
                ->itemsStorage
                ->getRoles();
        }

        if (is_string($filter)) {
            $items = array_filter(
                $items,
                fn(string $name) => str_contains(strtolower($name), strtolower($filter)),
                ARRAY_FILTER_USE_KEY
            );
        }

        uksort($items, fn(string $a, string $b) => $a <=> $b);

        if ($type === Item::TYPE_PERMISSION) {
            array_walk(
                $items,
                fn(Item &$item, $key, $ths) => $item = (new RbamItem($item))->withParents($ths->getGrantedBy($item)),
                $this
            );
        } else {
            $defaultRoles = $this
                ->manager
                ->getDefaultRoleNames()
            ;

            $guestRole = $this
                ->manager
                ->getGuestRoleName()
            ;

            array_walk(
                $items,
                fn(Item &$item, $key, $roles) => $item = (new RbamItem($item))
                    ->withIsDefaultRole(in_array($key, $roles['defaultRoles']))
                    ->withIsdGuestRole($key === $roles['guestRole'])
                ,
                compact('defaultRoles', 'guestRole')
            );
        }

        if ($request->getMethod() === Method::POST) {
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
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
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
    #[PermissionAttribute(item: RbamPermission::itemCreate)]
    public function create(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        ServerRequestInterface $request,
        RuleServiceInterface $ruleService,
    ): ResponseInterface
    {
        $type = $currentRoute->getArgument('type');

        $formModel = new ItemForm($this->translator, $this->itemsStorage, ItemForm::MODE_CREATE);

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            if ($type === Item::TYPE_PERMISSION) {
                $this
                    ->manager
                    ->addPermission(
                        (new Permission($formModel->getName()))
                            ->withDescription($formModel->getDescription())
                            ->withRuleName($formModel->getRuleName())
                    )
                ;
            } else {
                $this
                    ->manager
                    ->addRole(
                        (new Role($formModel->getName()))
                            ->withDescription($formModel->getDescription())
                            ->withRuleName($formModel->getRuleName())
                    )
                ;
            }

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
                    'rbam.item.view',
                    [
                        'name' => $formModel->getName(),
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
                    'ruleClasses' => $ruleService->getRuleClasses(),
                    'type' => $type,
                ],
            )
        ;
    }

    /**
     * Manage child roles or permissions of a role, and child permissions of a permission
     *
     * @param CurrentRoute $currentRoute
     * @return ResponseInterface
     */
    public function manageChildren(CurrentRoute $currentRoute): ResponseInterface
    {
        return $this->renderChildrenAndOrphans([
            'childType' => $currentRoute->getArgument('childType'),
            'parent' => $currentRoute->getArgument('name'),
            'type' => $currentRoute->getArgument('type')
        ]);
    }

    #[PermissionAttribute(item: RbamPermission::itemUpdate)]
    public function translate(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        ServerRequestInterface $request,
        TranslationServiceInterface $translationService,
    ): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');
        $type = $currentRoute->getArgument('type');

        if (!$this
            ->itemsStorage
            ->exists($name)
        ) {
            return $notFound->create();
        }

        $formModel = new TranslationForm();
        $item = $this
            ->itemsStorage
            ->get($name)
        ;

        if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
            $translations = [];

            foreach ($formModel->getTranslations() as $translation) {
                $translations[$translation->getLocale()]['rbac-item'][$name]
                    = $translation->getName()
                ;
                $translations[$translation->getLocale()]['rbac-item'][$item->getDescription()]
                    = $translation->getDescription()
                ;
            }

            $translationService->save($translations);

            return $this
                ->redirect
                ->toRoute('rbam.item.view', ['name' => $name, 'type' => $type])
                ->create()
            ;
        }

        if (!$formModel->hasTranslations()) {
            $formModel = $formModel->withTranslations(
                $translationService->getItemTranslations($item)
            );
        }

        return $this
            ->viewRenderer
            ->render(
                'translationForm',
                [
                    'formModel' => $formModel,
                    'item' => $item,
                    'type' => $type,
                ]
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
    #[PermissionAttribute(item: RbamPermission::itemUpdate)]
    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        RuleServiceInterface $ruleService,
        ServerRequestInterface $request,
        TranslationServiceInterface $translationService,
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

        $type = $item->getType();

        $formModel = new ItemForm($this->translator, $this->itemsStorage, ItemForm::MODE_UPDATE);

        if ($request->getMethod() === Method::POST) {
            if ($formHydrator->populateFromPostAndValidate($formModel, $request, strict: false)) {
                if ($type === Item::TYPE_PERMISSION) {
                    $newItem = (new Permission($formModel->getName()))
                        ->withDescription($formModel->getDescription())
                        ->withRuleName($formModel->getRuleName())
                        ->withCreatedAt($item->getCreatedAt())
                        ->withUpdatedAt(time())
                    ;

                    $this
                        ->manager
                        ->updatePermission($name, $newItem)
                    ;

                } else {
                    $newItem = (new Role($formModel->getName()))
                        ->withDescription($formModel->getDescription())
                        ->withRuleName($formModel->getRuleName())
                        ->withCreatedAt($item->getCreatedAt())
                        ->withUpdatedAt(time())
                    ;

                    $this
                        ->manager
                        ->updateRole($name, $newItem)
                    ;
                }

                $translationService
                    ->updateItem($item, $newItem)
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
                        'rbam.item.view',
                        [
                            'name' => $newItem->getName(),
                            'type' => $type,
                        ],
                    )
                    ->withStatusCode(Status::SEE_OTHER)
                    ->create()
                ;
            }
        }

        $formHydrator->populate(
            model: $formModel,
            data: [
                'description' => $item->getDescription(),
                'name' => $item->getName(),
                'ruleName' => $item->getRuleName(),
            ],
            strict: false,
            scope: '',
        );

        return $this
            ->viewRenderer
            ->render(
                'itemForm',
                [
                    'formModel' => $formModel,
                    'type' => $type,
                    'ruleClasses' => $ruleService->getRuleClasses(),
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
    #[PermissionAttribute(item: RbamPermission::itemView)]
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
        $users = $this->getPermittedUsers($item);

        $children = $this
            ->itemsStorage
            ->getAllChildPermissions($item->getName())
        ;
        uksort($children, fn(string $a, string $b) => $a <=> $b);
        array_walk($children, fn(Item &$item) => $item = new RbamItem($item));

        return $this
            ->viewRenderer
            ->render(
                'viewPermission',
                [
                    'children' => $children,
                    'diagram' => (new MermaidHierarchyDiagram(
                        $this->itemsStorage,
                        $this->translator,
                        $this->urlGenerator
                    ))
                        ->withItem($item)
                    ,
                    'item' => (new RbamItem($item))->withParents($this->getGrantedBy($item)),
                    'users' => $users,
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
            ->itemsStorage
            ->getAllChildPermissions($name)
        ;
        uksort($permissions, fn(string $a, string $b) => $a <=> $b);
        array_walk(
            $permissions,
            fn(Item &$item, $key, $ths) => $item = (new RbamItem($item))->withParents($ths->getGrantedBy($item)), $this
        );

        $children = $this
            ->itemsStorage
            ->getAllChildRoles($name)
        ;
        uksort($children, fn(string $a, string $b) => $a <=> $b);
        array_walk($children, fn(Item &$item) => $item = new RbamItem($item));

        return $this
            ->viewRenderer
            ->render(
                'viewRole',
                [
                    'assignments' => $assignments,
                    'children' => $children,
                    'diagram' => (new MermaidHierarchyDiagram(
                        $this->itemsStorage,
                        $this->translator,
                        $this->urlGenerator
                    ))
                        ->withItem($item)
                    ,
                    'item' => $item,
                    'permissions' => $permissions,
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
    public function addChild(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->itemsStorage
            ->addChild($parsedBody['parent'], $parsedBody['child'])
        ;

        return $this->renderChildrenAndOrphans($parsedBody, true);
    }

    public function assignments(ServerRequestInterface $request): ResponseInterface
    {
        ['name' => $name] = $request->getParsedBody();

        $item = $this
            ->itemsStorage
            ->getRole($name)
        ;

        $ancestors = $this
            ->itemsStorage
            ->getParents($name)
        ;

        $assignments = [];
        $rbacAssignments = $this->assignmentsStorage->getByItemNames([$name]);

        foreach ($this->userRepository
            ->findByIds(
                $this
                    ->manager
                    ->getUserIdsByRoleName($name),
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
     * Paginator for child roles and permissions of a role
     *
     * POST
     */
    public function childItems(CurrentRoute $currentRoute, ServerRequestInterface $request): ResponseInterface
    {
        $name = $currentRoute->getArgument('name');
        [
            'action_buttons' => $actionButtons,
            'header' => $header,
            'pagination_url' => $paginationUrl,
            'toolbar' => $toolbar,
            'type' => $type
        ]
            = $request->getParsedBody()
        ;

        if ($type === Item::TYPE_PERMISSION) {
            $items = $this
                ->manager
                ->getPermissionsByRoleName($name)
            ;
            uksort($items, fn(string $a, string $b) => $a <=> $b);
            array_walk(
                $items,
                fn(Item &$item, $key, $ths) => $item = (new RbamItem($item))->withParents($ths->getGrantedBy($item)),
                $this
            );
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
                    'actionButtons' => explode(',', $actionButtons),
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'noResultsText' => '',
                    'header' => $header ?? '',
                    'item' => $this
                        ->itemsStorage
                        ->get($name)
                    ,
                    'items' => $items,
                    'paginationUrl' => $paginationUrl,
                    'toolbar' => $toolbar,
                    'type' => $type,
                ]
            )
        ;
    }

    /**
     * Paginator for children on manage children page
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function children(ServerRequestInterface $request): ResponseInterface
    {
        ['child_type' => $childType, 'parent' => $parent, 'type' => $type] = $request->getParsedBody();

        if ($childType === Item::TYPE_PERMISSION) {
            $children = $this->itemsStorage->getAllChildPermissions($parent);
        } else {
            $children = $this->itemsStorage->getAllChildRoles($parent);
        }

        ksort($children);
        array_walk(
            $children,
            fn(&$child) =>
            $child = (new RbamItem($child))->withIsChild($this->manager->hasChild($parent, $child->getName()))
        );

        return $this
            ->viewRenderer
            ->renderPartial(
                '_children',
                [
                    'children' => $children,
                    'childType' => $childType,
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'parent' => $this->manager->getRole($parent),
                    'type' => $type,
                ],
            )
        ;
    }

    /**
     * Paginator for orphans on manage children page
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function orphans(ServerRequestInterface $request): ResponseInterface
    {
        ['child_type' => $childType, 'parent' => $parent, 'type' => $type] = $request->getParsedBody();

        if ($childType === Item::TYPE_PERMISSION) {
            $children = $this->manager->getPermissionsByRoleName($parent);
            $orphans = array_diff_key($this->itemsStorage->getPermissions(), $children);
        } else {
            $children = $this->manager->getChildRoles($parent);
            $orphans = array_diff_key($this->itemsStorage->getRoles(), $children);
        }

        ksort($orphans);

        return $this
            ->viewRenderer
            ->renderPartial(
                '_orphans',
                [
                    'childType' => $childType,
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'orphans' => $orphans,
                    'parent' => $this->manager->getRole($parent),
                    'type' => $type,
                ],
            )
        ;
    }

    public function permittedUsers(ServerRequest $request): ResponseInterface
    {
        ['item' => $item] = $request->getParsedBody();
        $permission = $this->itemsStorage->getPermission($item);

        return $this
            ->viewRenderer
            ->renderPartial(
                '_permittedUsers',
                [
                    'currentPage' => (int) ArrayHelper::getValue($request->getQueryParams(), 'page', 1),
                    'permission' => $permission,
                    'users' => $this->getPermittedUsers($permission),
                ]
            )
        ;
    }

    /**
     * Remove - delete - an item - Permission or Role
     *
     * If the item has child items the parent/child relationships are deleted, but the child items are not
     *
     * @param ServerRequestInterface $request
     * @param TranslationServiceInterface $translationService
     * @return ResponseInterface
     * @psalm-suppress PossiblyNullArgument
     */
    #[PermissionAttribute(item: RbamPermission::itemRemove)]
    public function remove(
        ServerRequestInterface $request,
        TranslationServiceInterface $translationService
    ): ResponseInterface
    {
        [
            'action_buttons' => $actionButtons,
            'header' => $header,
            'item' => $item,
            'pagination_url' => $paginationUrl,
            'toolbar' => $toolbar,
            'type' => $type
        ]
            = $request->getParsedBody()
        ;

        foreach ($this->itemsStorage->getPermissions() as $permission) {
            if ($this->itemsStorage->hasChild($item, $permission->getName())) {
                $this->itemsStorage->removeChild($item, $permission->getName());
            }
        }

        foreach ($this->itemsStorage->getRoles() as $role) {
            if ($this->itemsStorage->hasChild($item, $role->getName())) {
                $this->itemsStorage->removeChild($item, $role->getName());
            }
        }

        $method = 'get' . ucfirst($type);
        $itemToRemove= $this
            ->itemsStorage
            ->$method($item)
        ;

        $method = 'remove' . ucfirst($type);
        $this
            ->manager
            ->$method($item)
        ;

        $translationService->deleteItem($itemToRemove);

        $items = $this->getViewParameters($item, $type)['items'];

        foreach ($items as $i => $item) {
            $items[$i] = new RbamItem($item);
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_items',
                [
                    'actionButtons' => explode(',', $actionButtons),
                    'currentPage' => 1,
                    'header' => $header ?? '',
                    'item' => null,
                    'items' => $items,
                    'noResultsText' => '',
                    'paginationUrl' => $paginationUrl,
                    'toolbar' => $toolbar ?? '',
                    'type' => $type,
                    'user' => null,
                ]
            )
        ;
    }

    /**
     * Remove a child item, or all children if a child is not specified, from a Role
     *
     * Child items are not deleted, only the parent/child relationship
     *
     * POST
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function removeChild(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        ['child' => $child, 'childType' => $childType, 'parent' => $parent] = $parsedBody;

        if (isset($child)) {
            $this
                ->manager
                ->removeChild($parent, $child);
        } elseif ($childType === Item::TYPE_ROLE) {
            $this
                ->manager
                ->removeChildren($parent);
        } else { // need to remove permissions one by one
            foreach ($this->manager->getPermissionsByRoleName($parent) as $permission) {
                $this
                    ->manager
                    ->removeChild($parent, $permission->getName());
            }
        }

        return $this->renderChildrenAndOrphans($parsedBody, true);
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
                    $permittedUsers[] = (new RbamUser($user))
                        ->withAssignment($assignment)
                        ->withRole($ancestor)
                    ;
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

    private function renderChildrenAndOrphans(array $params, bool $renderPartial = false): ResponseInterface
    {
        ['childType' => $childType, 'parent' => $parent, 'type' => $type] = $params;

        if ($type === Item::TYPE_PERMISSION) {
            $children = $this->itemsStorage->getAllChildPermissions($parent);
            $orphans = array_diff_key($this->itemsStorage->getPermissions(), $children, [$parent => $parent]);
        } elseif ($childType === Item::TYPE_PERMISSION) {
            $children = $this->itemsStorage->getAllChildPermissions($parent);
            $orphans = array_diff_key($this->itemsStorage->getPermissions(), $children);
        } else {
            $children = $this->itemsStorage->getAllChildRoles($parent);
            $orphans = array_diff_key($this->itemsStorage->getRoles(), $children);
        }

        ksort($children);
        array_walk(
            $children,
            fn(&$child) =>
                $child = (new RbamItem($child))->withIsChild($this->manager->hasChild($parent, $child->getName()))
        );

        array_filter(
            $orphans,
            fn(string $orphan) => $this->manager->canAddChild($parent, $orphan),
            ARRAY_FILTER_USE_KEY
        );
        ksort($orphans);

        $viewParameters = [
            'children' => $children,
            'childType' => $childType,
            'orphans' => $orphans,
            'parent' => $this->itemsStorage->get($parent),
            'type' => $type,
        ];

        if ($renderPartial) {
            return $this
                ->viewRenderer
                ->renderPartial('manageChildren', $viewParameters);
        }

        return $this
            ->viewRenderer
            ->render('manageChildren', $viewParameters);
    }
}