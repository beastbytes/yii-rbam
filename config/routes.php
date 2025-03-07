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
use Yiisoft\Router\Route;

return [
    Route::get('/rbam/{type: permissions|roles}')
        ->name('rbam.itemIndex')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->action([ItemController::class, 'index']),
    Route::methods([Method::GET, Method::POST], '/rbam/create/{type: permission|role}')
        ->name('rbam.createItem')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemCreate))
        ->action([ItemController::class, 'create']),
    Route::methods([Method::GET, Method::POST], '/rbam/{name: [a-z][\w]*}/child-{type: role}s')
        ->name('rbam.childRoles')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->action([ItemController::class, 'children']),
    Route::methods([Method::GET, Method::POST], '/rbam/{name: [a-z][\w]*}/{type: permission}s')
        ->name('rbam.rolePermissions')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->action([ItemController::class, 'children']),
    Route::post('/rbam/remove/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.removeItem')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemRemove))
        ->action([ItemController::class, 'remove']),
    Route::methods([Method::GET, Method::POST], '/rbam/update/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.updateItem')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([ItemController::class, 'update']),
    Route::get('/rbam/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.viewItem')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->action([ItemController::class, 'view']),
    Route::post('/rbam/add_child')
        ->name('rbam.addChild')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([ItemController::class, 'addChild']),
    Route::post('/rbam/remove_all_children')
        ->name('rbam.removeAllChildren')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([ItemController::class, 'removeAllChildren']),
    Route::post('/rbam/remove_child')
        ->name('rbam.removeChild')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([ItemController::class, 'removeChild']),

    Route::get('/rbam')
        ->name('rbam.rbam')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RbamIndex))
        ->action([RbamController::class, 'index']),
    Route::get('/rbam/init')
        ->name('rbam.init')
        ->action([RbamController::class, 'init']),

    Route::methods([Method::GET, Method::POST], '/rbam/create/rule')
        ->name('rbam.createRule')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleCreate))
        ->action([RuleController::class, 'create']),
    Route::post('/rbam/delete/rule')
        ->name('rbam.deleteRule')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleDelete))
        ->action([RuleController::class, 'delete']),
    Route::get('/rbam/rules')
        ->name('rbam.ruleIndex')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleView))
        ->action([RuleController::class, 'index']),
    Route::methods([Method::GET, Method::POST],'/rbam/update/rule/{name: [a-z][\w]*}')
        ->name('rbam.updateRule')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleUpdate))
        ->action([RuleController::class, 'update']),
    Route::get('/rbam/rule/{name: [a-z][\w]*}')
        ->name('rbam.viewRule')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleView))
        ->action([RuleController::class, 'view']),

    Route::get('/rbam/users')
        ->name('rbam.userIndex')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::UserView))
        ->action([UserController::class, 'index']),
    Route::get('/rbam/user/{id: [1-9]\d*}')
        ->name('rbam.viewUser')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::UserView))
        ->action([UserController::class, 'view']),
    Route::post('/rbam/assign_role')
        ->name('rbam.assignRole')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([UserController::class, 'assignRole']),
    Route::post('/rbam/revoke_assignment')
        ->name('rbam.revokeAssignment')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([UserController::class, 'revokeAssignment']),
    Route::post('/rbam/revoke_all_assignments')
        ->name('rbam.revokeAllAssignments')
        //->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([UserController::class, 'revokeAllAssignments']),
];