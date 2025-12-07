<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Controller\ItemController;
use BeastBytes\Yii\Rbam\Controller\RbamController;
use BeastBytes\Yii\Rbam\Controller\RuleController;
use BeastBytes\Yii\Rbam\Controller\UserController;
use BeastBytes\Yii\Rbam\Middleware\AccessChecker;
use BeastBytes\Yii\Rbam\Permission;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create('/rbam')
        ->namePrefix('rbam.')
        ->routes(
            Route::get('/')
                ->name('rbam')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::index()))
                ->action([RbamController::class, 'index'])
            ,
            Route::methods([Method::GET, Method::POST], '/rbam/initialise')
                ->name('initialise')
                ->action([RbamController::class, 'initialise'])
            ,

            Route::get('/users')
                ->name('user.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::userView()))
                ->action([UserController::class, 'index'])
            ,
            Group::create('/user')
                ->namePrefix('user.')
                ->routes(
                    Route::get('/{id: [1-9]\d*}')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::userView()))
                        ->action([UserController::class, 'view'])
                    ,
                    Route::post('/assign_role')
                        ->name('assign-role')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([UserController::class, 'assignRole'])
                    ,
                    Route::post('/revoke_assignment')
                        ->name('revoke-assignment')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([UserController::class, 'revokeAssignment'])
                    ,
                    Route::post('/revoke_all_assignments')
                        ->name('revoke-all-assignments')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([UserController::class, 'revokeAllAssignments'])
                    ,
                    Route::post('/items/{type: permissions|roles}')
                        ->name('items')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([UserController::class, 'items'])
                    ,
                )
            ,
            Route::get('/{type: permissions|roles}')
                ->name('item.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                ->action([ItemController::class, 'index'])
            ,
            Group::create('/item')
                ->namePrefix('item.')
                ->routes(
                    Route::methods([Method::GET, Method::POST], '/create/{type: permission|role}')
                        ->name('create')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemCreate()))
                        ->action([ItemController::class, 'create'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/{name: [a-z][\w]*}/child-{type: role}s')
                        ->name('child-roles')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                        ->action([ItemController::class, 'children'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/{name: [a-z][\w]*}/{type: permission}s')
                        ->name('role-permissions')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                        ->action([ItemController::class, 'children'])
                    ,
                    Route::post('/rbam/pagination/assignments')
                        ->name('rbam.assignment-pagination')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                        ->action([ItemController::class, 'assignmentPagination'])
                    ,
                    Route::post('/rbam/pagination/items')
                        ->name('itemPagination')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                        ->action([ItemController::class, 'itemPagination'])
                    ,
                    Route::post('/remove/{type: permission|role}/{name: [a-z][\w]*}')
                        ->name('remove')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemRemove()))
                        ->action([ItemController::class, 'remove'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/update/{type: permission|role}/{name: [a-z][\w]*}')
                        ->name('update')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([ItemController::class, 'update'])
                    ,
                    Route::methods([Method::GET, Method::POST], '/{type: permission|role}/{name: [a-z][\w]*}')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemView()))
                        ->action([ItemController::class, 'view'])
                    ,
                    Route::post('/add_child')
                        ->name('add-child')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([ItemController::class, 'addChild'])
                    ,
                    Route::post('/remove_all_children')
                        ->name('remove-all-children')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([ItemController::class, 'removeAllChildren'])
                    ,
                    Route::post('/remove_child')
                        ->name('remove-child')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::itemUpdate()))
                        ->action([ItemController::class, 'removeChild'])
                    ,
                )
            ,
            Route::get('/rules')
                ->name('rule.index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleView()))
                ->action([RuleController::class, 'index'])
            ,
            Group::create('/rule')
                ->namePrefix('rule.')
                ->routes(
                    Route::methods([Method::GET, Method::POST], '/create')
                        ->name('create')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleCreate()))
                        ->action([RuleController::class, 'create'])
                    ,
                    Route::post('/delete')
                        ->name('delete')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleDelete()))
                        ->action([RuleController::class, 'delete'])
                    ,
                    Route::methods([Method::GET, Method::POST],'/update/{name: [a-z][\w]*}/rule')
                        ->name('update')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleUpdate()))
                        ->action([RuleController::class, 'update'])
                    ,
                    Route::get('/{name: [a-z][\w]*}/rule')
                        ->name('view')
                        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ruleView()))
                        ->action([RuleController::class, 'view'])
                    ,
                )
        )
    ,
];