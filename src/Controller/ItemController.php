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
use BeastBytes\Yii\Rbam\Form\ItemForm;
use BeastBytes\Yii\Rbam\ItemTypeService;
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
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class ItemController
{
    private const RBAM_ROLE = 'RbamItemsManager';

    public const TYPE = 'type';

    public function __construct(
        private readonly FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
        private readonly Redirect $redirect,
        private TranslatorInterface $translator,
        private ViewRenderer $viewRenderer,
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

    #[PermissionAttribute(
        name: RbamPermission::RbacItemView,
        parent: self::RBAM_ROLE
    )]
    public function index(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        /** @psalm-suppress PossiblyNullArgument */
        $type = $this
            ->inflector
            ->toSingular($currentRoute
                ->getArgument('type'),
            )
        ;

        $items = match($type) {
            Item::TYPE_PERMISSION => $this
                ->itemsStorage
                ->getPermissions(),
            Item::TYPE_ROLE => $this
                ->itemsStorage
                ->getRoles(),
        };

        uksort($items, function(string $a, string $b) {
            return $a <=> $b;
        });

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'items' => $items,
                    'itemsStorage' => $this->itemsStorage,
                    'type' => $type,
                ],
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemCreate,
        parent: self::RBAM_ROLE
    )]
    public function create(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        ServerRequestInterface $request,
        RuleServiceInterface $ruleService,
    ): ResponseInterface
    {
        $type = $currentRoute
            ->getArgument('type')
        ;

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
                            'flash.item-created',
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

    #[PermissionAttribute(
        name: RbamPermission::RbacItemView,
        parent: self::RBAM_ROLE
    )]
    public function children(CurrentRoute $currentRoute): ResponseInterface
    {
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;
        $type = $currentRoute
            ->getArgument('type')
        ;

        return $this
            ->viewRenderer
            ->render(
                'children',
                $this->getViewParameters($name, $type),
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemView,
        parent: self::RBAM_ROLE
    )]
    public function assignmentPagination(
        AssignmentsStorageInterface $assignmentsStorage,
        ServerRequestInterface $request
    ): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        /** @var array{name:string, id:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        return $this->viewRenderer->render(
            '_assignments',
            [
                'assignments' => $assignmentsStorage->getByItemNames([$parsedBody['name']]),
                'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                'item' => $this->manager->getRole($parsedBody['name']),
            ]
        );
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemView,
        parent: self::RBAM_ROLE
    )]
    public function itemPagination(
        ServerRequestInterface $request
    ): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        /** @var array{name:string, id:string} $parsedBody */
        $parsedBody = $request->getParsedBody();

        if ($parsedBody['id'] === Item::TYPE_PERMISSION) {
            $items = $this
                ->manager
                ->getPermissionsByRoleName($parsedBody['name'])
            ;
        } else {
            $items = $this
                ->manager
                ->getChildRoles($parsedBody['name'])
            ;
        }

        return $this
            ->viewRenderer
            ->renderPartial(
                '_items',
                [
                    'actionButtons' => explode(',', $parsedBody['actionButtons']),
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'emptyText' => '',
                    'header' => $parsedBody['header'],
                    'item' => $this->manager->getRole($parsedBody['name']),
                    'items' => $items,
                    'itemsStorage' => $this->itemsStorage,
                    'toolbar' => $parsedBody['toolbar'],
                    'type' => $parsedBody['id']
                ]
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
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
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

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
                                'flash.item-updated',
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

    #[PermissionAttribute(
        name: RbamPermission::RbacItemView,
        parent: self::RBAM_ROLE
    )]
    public function view(
        AssignmentsStorageInterface $assignmentsStorage,
        CurrentRoute $currentRoute,
        NotFound $notFound,
        ServerRequest $request,
        UserRepositoryInterface $userRepository,
    ): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        /** @psalm-suppress PossiblyNullArgument */
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

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

        if ($item?->getType() === Item::TYPE_ROLE) {
            $assignments = $assignmentsStorage->getByItemNames([$name]);

            $permissions = $this
                ->manager
                ->getPermissionsByRoleName($name)
            ;

            $roles = $this
                ->manager
                ->getChildRoles($name)
            ;

            $users = $userRepository
                ->findByIds(
                    $this
                        ->manager
                        ->getUserIdsByRoleName($name),
                )
            ;
        } else {
            $assignments = $permissions = $roles = $userIds = [];

            $ancestors = $this->itemsStorage->getParents($name);

            foreach ($ancestors as $ancestor) {
                if (
                    $ancestor->getType() === Item::TYPE_ROLE
                    && $this->manager->hasChild($ancestor->getName(), $name)
                ) {
                    $ids = $this
                        ->manager
                        ->getUserIdsByRoleName($ancestor->getName())
                    ;
                    array_push($userIds, ...$ids);
                }
            }

            $users = $userRepository
                ->findByIds(array_unique($userIds, SORT_NUMERIC))
            ;
        }

        $ancestors = $this
            ->itemsStorage
            ->getParents($name)
        ;

        return $this
            ->viewRenderer
            ->render(
                'view',
                [
                    'ancestors' => $ancestors,
                    'assignments' => $assignments,
                    'assignmentsStorage' => $assignmentsStorage,
                    'item' => $item,
                    'itemsStorage' => $this->itemsStorage,
                    'permissions' => $permissions,
                    'roles' => $roles,
                    'users' => $users,
                ],
            )
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
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
        name: RbamPermission::RbacItemUpdate,
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

    #[PermissionAttribute(
        name: RbamPermission::RbacItemUpdate,
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

    /** @psalm-suppress PossiblyNullArgument */
    #[PermissionAttribute(
        name: RbamPermission::RbacItemRemove,
        parent: self::RBAM_ROLE
    )]
    public function remove(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $type = ItemTypeService::getItemType($this
            ->itemsStorage
            ->get($parsedBody['name']),
        );

        foreach ($this->itemsStorage->getRoles() as $role) {
            if ($this->itemsStorage->hasChild($parsedBody['name'], $role->getName())) {
                $this->itemsStorage->removeChild($parsedBody['name'], $role->getName());
            }
        }

        $method = 'remove' . ucfirst($type);
        $this
            ->manager
            ->$method($parsedBody['name'])
        ;

        $this->flash->add(
            'success',
            $this
                ->translator
                ->translate(
                    'flash.item-deleted',
                    [
                        'name' => $parsedBody['name'],
                        'type' => $type,
                    ],
                ),
        );

        return $this
            ->redirect
            ->toRoute('rbam.itemIndex')
            ->create()
        ;
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