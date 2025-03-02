<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Controller\AssignmentController;
use BeastBytes\Yii\Rbam\Controller\ChildrenController;
use BeastBytes\Yii\Rbam\Controller\ItemController;
use BeastBytes\Yii\Rbam\Controller\RbamController;
use BeastBytes\Yii\Rbam\Controller\RuleController;
use BeastBytes\Yii\Rbam\Controller\UserController;
use BeastBytes\Yii\Rbam\Middleware\AccessChecker;
use BeastBytes\Yii\Rbam\Permission;
use Yiisoft\Http\Method;
use Yiisoft\Router\Route;

return [
    Route::post('/rbam/assign')
        ->name('rbam.assign')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::AssignmentAssign))
        ->action([AssignmentController::class, 'assign']),
    Route::post('/rbam/revoke')
        ->name('rbam.revoke')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::AssignmentRevoke))
        ->action([AssignmentController::class, 'revoke']),
    Route::post('/rbam/revoke_all')
        ->name('rbam.revokeAll')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::AssignmentRevoke))
        ->action([AssignmentController::class, 'revokeAll']),

    Route::post('/rbam/add')
        ->name('rbam.addChild')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ChildAdd))
        ->action([ChildrenController::class, 'add']),
    Route::post('/rbam/remove')
        ->name('rbam.removeChild')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ChildRemove))
        ->action([ChildrenController::class, 'remove']),
    Route::post('/rbam/remove_all')
        ->name('rbam.removeAll')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ChildRemove))
        ->action([ChildrenController::class, 'removeAll']),

    Route::get('/rbam/{type: permissions|roles}')
        ->name('rbam.itemIndex')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->action([ItemController::class, 'index']),
    Route::methods([Method::GET, Method::POST], '/rbam/create/{type: permission|role}')
        ->name('rbam.createItem')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemCreate))
        ->action([ItemController::class, 'create']),
    Route::methods([Method::GET, Method::POST], '/rbam/children/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.children')
        ->action([ItemController::class, 'children']),
    Route::post('/rbam/remove/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.removeItem')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemRemove))
        ->action([ItemController::class, 'remove']),
    Route::methods([Method::GET, Method::POST], '/rbam/update/{type: permission|role}/{name: [a-z][\w]*}')
        ->name('rbam.updateItem')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemUpdate))
        ->action([ItemController::class, 'update']),
    Route::get('/rbam/{type: permission|role}/{name: [a-z][\w]*}')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::ItemView))
        ->name('rbam.viewItem')
        ->action([ItemController::class, 'view']),

    Route::get('/rbam')
        ->name('rbam.rbam')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RbamIndex))
        ->action([RbamController::class, 'index']),
    Route::get('/rbam/init')
        ->name('rbam.init')
        ->action([RbamController::class, 'init']),

    Route::methods([Method::GET, Method::POST], '/rbam/create/rule')
        ->name('rbam.createRule')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleCreate))
        ->action([RuleController::class, 'create']),
    Route::get('/rbam/rules')
        ->name('rbam.ruleIndex')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleView))
        ->action([RuleController::class, 'index']),
    Route::methods([Method::GET, Method::POST],'/rbam/update/rule/{name: [a-z][\w]*}')
        ->name('rbam.updateRule')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleUpdate))
        ->action([RuleController::class, 'update']),
    Route::get('/rbam/rule/{name: [a-z][\w]*}')
        ->name('rbam.viewRule')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::RuleView))
        ->action([RuleController::class, 'view']),

    Route::get('/rbam/users')
        ->name('rbam.userIndex')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::UserView))
        ->action([UserController::class, 'index']),
    Route::get('/rbam/user/{id: [1-9]\d*}')
        ->name('rbam.viewUser')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission(Permission::UserView))
        ->action([UserController::class, 'view']),
];