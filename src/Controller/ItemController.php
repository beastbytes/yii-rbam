<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Form\ItemForm;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

class ItemController
{
    public const TYPE = 'type';

    public function __construct(
        private FlashInterface $flash,
        private Inflector $inflector,
        private ItemsStorageInterface $itemsStorage,
        private ManagerInterface $manager,
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

    public function index(CurrentRoute $currentRoute): ResponseInterface
    {
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
            ->render('index', ['items' => $items, 'type' => $type])
        ;
    }

    public function add(
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

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populate($formModel, $request->getParsedBody())
            && $formModel->isValid()
        ) {
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
            ->render('itemForm', [
                'formModel' => $formModel,
                'ruleNames' => $ruleService->getRuleNames(),
                'type' => $type
            ])
        ;
    }

    public function children(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request
    ): ResponseInterface
    {
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

        $parent = $this
            ->itemsStorage
            ->getRole($name)
        ;

        return $this
            ->viewRenderer
            ->render('children', [
                'children' => $children,
                'descendants' => $descendants,
                'items' => $items,
                'parent' => $parent
            ])
        ;
    }

    /** @psalm-suppress PossiblyNullArgument */
    public function remove(
        CurrentRoute $currentRoute,
        NotFound $notFound,
        Redirect $redirect
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

        return $redirect
            ->toRoute('rbam.items', ['type' => $type])
            ->withStatusCode(Status::SEE_OTHER)
            ->create()
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
            if (
                $formHydrator->populate($formModel, $request->getParsedBody())
                && $formModel->isValid()
            ) {
                /** @psalm-suppress PossiblyNullArgument */
                $method = 'update' . ucfirst($type);
                $item =  $item
                    ->withName($formModel->getName())
                    ->withDescription($formModel->getDescription())
                    ->withRuleName($formModel->getRuleName())
                ;

                $this
                    ->manager
                    ->$method($name, $item)
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
            $permissions = $roles = $users = [];
        }

        return $this
            ->viewRenderer
            ->render('view', [
                'item' => $item,
                'itemStorage' => $this->itemsStorage,
                'permissions' => $permissions,
                'roles' => $roles,
                'users' => $users
            ])
        ;
    }
}
