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
use Yiisoft\Http\Method;
use Yiisoft\Router\Route;

return [
    Route::post('/rbam/assign')
         ->action([AssignmentController::class, 'assign'])
         ->name('rbam.assign'),
    Route::post('/rbam/revoke')
         ->action([AssignmentController::class, 'revoke'])
         ->name('rbam.revoke'),
    Route::post('/rbam/revoke_all')
         ->action([AssignmentController::class, 'revokeAll'])
         ->name('rbam.revokeAll'),

    Route::post('/rbam/add')
         ->action([ChildrenController::class, 'add'])
         ->name('rbam.addChild'),
    Route::post('/rbam/remove')
         ->action([ChildrenController::class, 'remove'])
         ->name('rbam.removeChild'),
    Route::post('/rbam/remove_all')
         ->action([ChildrenController::class, 'removeAll'])
         ->name('rbam.removeAll'),

    Route::get('/rbam/{type: permissions|roles}')
         ->action([ItemController::class, 'index'])
         ->name('rbam.itemIndex'),
    Route::methods([Method::GET, Method::POST], '/rbam/create/{type: permission|role}')
         ->action([ItemController::class, 'create'])
         ->name('rbam.createItem'),
    Route::methods([Method::GET, Method::POST], '/rbam/children/{type: permission|role}/{name: [a-z][\w]*}')
         ->action([ItemController::class, 'children'])
         ->name('rbam.children'),
    Route::post('/rbam/remove/{type: permission|role}/{name: [a-z][\w]*}')
         ->action([ItemController::class, 'remove'])
         ->name('rbam.removeItem'),
    Route::methods([Method::GET, Method::POST], '/rbam/update/{type: permission|role}/{name: [a-z][\w]*}')
         ->action([ItemController::class, 'update'])
         ->name('rbam.updateItem'),
    Route::get('/rbam/{type: permission|role}/{name: [a-z][\w]*}')
         ->action([ItemController::class, 'view'])
         ->name('rbam.viewItem'),

    Route::get('/rbam')
         ->action([RbamController::class, 'index'])
         ->name('rbam.rbam'),
    Route::methods([Method::GET, Method::POST], '/rbam/create/rule')
         ->action([RuleController::class, 'create'])
         ->name('rbam.createRule'),
    Route::get('/rbam/rules')
         ->action([RuleController::class, 'index'])
         ->name('rbam.ruleIndex'),
    Route::methods([Method::GET, Method::POST],'/rbam/update/rule/{name: [a-z][\w]*}')
         ->action([RuleController::class, 'update'])
         ->name('rbam.updateRule'),
    Route::get('/rbam/rule/{name: [a-z][\w]*}')
         ->action([RuleController::class, 'view'])
         ->name('rbam.viewRule'),

    Route::get('/rbam/users')
         ->action([UserController::class, 'index'])
         ->name('rbam.userIndex'),
    Route::get('/rbam/user/{id: [1-9]\d*}/assignments')
         ->action([UserController::class, 'assignments'])
         ->name('rbam.userAssignments'),
    Route::get('/rbam/user/{id: [1-9]\d*}')
         ->action([UserController::class, 'view'])
         ->name('rbam.viewUser'),
];