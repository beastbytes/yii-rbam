<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Item\ItemController;
use BeastBytes\Yii\Rbam\Middleware\AccessChecker;
use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\RbamController;
use BeastBytes\Yii\Rbam\Rule\RuleController;
use BeastBytes\Yii\Rbam\User\UserController;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create('/rbam')
        ->namePrefix('rbam.')
        ->routes(
            Route::get('')
                ->name('rbam')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::index))
                ->action([RbamController::class, 'index'])
            ,
            Route::post('/clear')
                ->name('clear')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::clear))
                ->action([RbamController::class, 'clear'])
            ,
            Route::methods([Method::GET, Method::POST], '/initialise')
                ->name('initialise')
                ->action([RbamController::class, 'initialise'])
            ,
            // Items
            Route::methods([Method::GET, Method::POST], '/{type: permissions|roles}')
                ->name('item.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView))
                ->action([ItemController::class, 'index'])
            ,
            Group::create('/{type: permission|role}')
                ->namePrefix('item.')
                ->routes(
                    Route::methods([Method::GET, Method::POST], '/create')
                        ->name('create')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemCreate))
                        ->action([ItemController::class, 'create'])
                    ,
                    Route::post('/{name: \w.*}/child-items')
                        ->name('child-items')
                        ->action([ItemController::class, 'childItems'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/{name: \w.*}/manage-children/{childType: permission|role}')
                        ->name('manage-children')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView))
                        ->action([ItemController::class, 'manageChildren'])
                    ,
                    Route::post('/{name: \w.*}/children/{childType: permission|role}')
                        ->name('children')
                        ->action([ItemController::class, 'children'])
                    ,
                    Route::post('/{name: \w.*}/orphans/{childType: permission|role}')
                        ->name('orphans')
                        ->action([ItemController::class, 'orphans'])
                    ,
                    Route::post('/assignments')
                        ->name('assignments')
                        ->action([ItemController::class, 'assignments'])
                    ,
                    Route::post('/permitted_users')
                        ->name('permitted-users')
                        ->action([ItemController::class, 'permittedUsers'])
                    ,
                    Route::post('/{name: \w.*}/remove')
                        ->name('remove')
                        ->action([ItemController::class, 'remove'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/{name: \w.*}/update')
                        ->name('update')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate))
                        ->action([ItemController::class, 'update'])
                    ,
                    Route::post('/{parent: \w.*}/add_child/{child: \w.*}')
                        ->name('add-child')
                        ->action([ItemController::class, 'addChild'])
                    ,
                    Route::post('/{parent: \w.*}/remove_child[/{child: \w.*}]')
                        ->name('remove-child')
                        ->action([ItemController::class, 'removeChild'])
                    ,
                    Route::get('/{name: \w.*}')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView))
                        ->action([ItemController::class, 'view'])
                    ,
                )
            ,
            // Rules
            Route::methods([Method::GET, Method::POST], '/rules')
                ->name('rule.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleView))
                ->action([RuleController::class, 'index'])
            ,
            Group::create('/rule')
                ->namePrefix('rule.')
                ->routes(
                    Route::methods([Method::GET, Method::POST], '/create')
                        ->name('create')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleCreate))
                        ->action([RuleController::class, 'create'])
                    ,
                    Route::post('/items') // Items pagination
                        ->name('items')
                        ->action([RuleController::class, 'items'])
                    ,
                    Route::post('/{name: \w.*}/delete')
                        ->name('delete')
                        ->action([RuleController::class, 'delete'])
                    ,
                    Route::methods([Method::GET, Method::POST],'/{name: \w.*}/update')
                        ->name('update')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleUpdate))
                        ->action([RuleController::class, 'update'])
                    ,
                    Route::get('/{name: \w.*}')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleView))
                        ->action([RuleController::class, 'view'])
                    ,
                )
            ,
            // Users
            Route::methods([Method::GET, Method::POST], '/users')
                ->name('user.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::userView))
                ->action([UserController::class, 'index'])
            ,
            Group::create('/user')
                ->namePrefix('user.')
                ->routes(
                    Route::get('/{id: [1-9]\d*}')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::userView))
                        ->action([UserController::class, 'view'])
                    ,
                    Route::post('/assign')
                        ->name('assign-role')
                        ->action([UserController::class, 'assign'])
                    ,
                    Group::create('/assignment')
                        ->namePrefix('assignment.')
                        ->routes(
                            Route::post('/revoke')
                                ->name('revoke')
                                ->action([UserController::class, 'revoke'])
                            ,
                        )
                    ,
                    Route::post('/permissions[/{page: [1-9]\d*}]')
                        ->name('permissions')
                        ->action([UserController::class, 'permissions'])
                    ,
                    Route::post('/roles/{status: assigned|unassigned}[/{page: [1-9]\d*}]')
                        ->name('roles')
                        ->action([UserController::class, 'roles'])
                    ,
                )
            ,
        )
    ,
];