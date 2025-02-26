<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Form\ItemForm;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseFactoryInterface;
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

class ItemController
{
    public const TYPE = 'type';

    public function __construct(
        private FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
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
            ->withViewPath('@views/item')
        ;
    }

    public function index(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        /** @psalm-suppress PossiblyNullArgument */
        $type = $this
            ->inflector
            ->toSingular($currentRoute
                ->getArgument('type')
            )
        ;

        $items = match($type) {
            Item::TYPE_PERMISSION => $this
                ->itemsStorage
                ->getPermissions(),
            Item::TYPE_ROLE => $this
                ->itemsStorage
                ->getRoles()
        };

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'items' => $items,
                    'itemsStorage' => $this->itemsStorage,
                    'pageSize' => (int) ArrayHelper::getValue($queryParams, 'pagesize', 20),
                    'type' => $type
                ]
            )
        ;
    }

    public function create(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request,
        RuleServiceInterface $ruleService
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
            $this
                ->manager
                ->$method($item
                    ->withDescription($formModel->getDescription())
                    ->withRuleName($formModel->getRuleName())
                    ->withCreatedAt(time())
                    ->withUpdatedAt(time())
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
                                'type' => ucfirst($type)
                            ]
                        )
                )
            ;

            return $redirect
                ->toRoute(
                    'rbam.viewItem',
                    [
                        'name' => $this
                            ->inflector
                            ->toSnakeCase($item->getName()),
                        'type' => $type
                    ]
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
                    'type' => $type
                ]
            )
        ;
    }

    public function children(CurrentRoute $currentRoute, ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;
        $type = $currentRoute
            ->getArgument('type')
        ;

        $children = $this
            ->itemsStorage
            ->getDirectChildren($name)
        ;

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
        }

        $ancestors = $this
            ->itemsStorage
            ->getParents($name)
        ;

        $parent = $this
            ->itemsStorage
            ->getRole($name)
        ;

        $ancestors[] = $parent;

        return $this
            ->viewRenderer
            ->render(
                'children',
                [
                    'ancestors' => $ancestors,
                    'children' => $children,
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'descendants' => $descendants,
                    'items' => $items,
                    'pageSize' => (int) ArrayHelper::getValue($queryParams, 'pagesize', 20),
                    'parent' => $parent,
                    'type' => $type,
                ]
            )
        ;
    }

    /** @psalm-suppress PossiblyNullArgument */
    public function remove(
        CurrentRoute $currentRoute,
        ResponseFactoryInterface $responseFactory
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
            return $responseFactory
                ->createResponse(Status::NOT_FOUND)
            ;
        }

        $type = $this
            ->itemsStorage
            ->get($name)
            ?->getType()
        ;

        $method = 'remove' . ucfirst($type);
        $this
            ->manager
            ->$method($name)
        ;

        return $responseFactory
            ->createResponse(Status::OK)
        ;
    }

    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        Redirect $redirect,
        RuleServiceInterface $ruleService,
        ServerRequestInterface $request
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

        $type = $item->getType();

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
                                    'type' => ucfirst($type)
                                ]
                            )
                    )
                ;

                return $redirect
                    ->toRoute(
                        'rbam.viewItem',
                        [
                            'name' => $this
                                ->inflector
                                ->toSnakeCase($item->getName()),
                            'type' => $type
                        ]
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
                scope: ''
            );
        }

        return $this
            ->viewRenderer
            ->render(
                'itemForm',
                [
                    'formModel' => $formModel,
                    'type' => $type,
                    'ruleNames' => $ruleService->getRuleNames()
                ]
            )
        ;
    }

    public function view(
        AssignmentsStorageInterface $assignmentsStorage,
        CurrentRoute $currentRoute,
        NotFound $notFound,
        UserRepositoryInterface $userRepository
    ): ResponseInterface
    {
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
                        ->getUserIdsByRoleName($name)
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
                ]
            )
        ;
    }
}