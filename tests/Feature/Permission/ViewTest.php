<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\Rbac\Role;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('View a Permission', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::itemView->getItemName())
    ));
    $page->assertSee('Name');
    $page->assertSee(Permission::itemView->getItemName());
    $page->assertSee('Description');
    $page->assertSee('View Permissions and Roles');
    $page->assertSee('Granted By');
    $page->assertSee(Role::itemManager->getItemName());
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');

    $page->assertSee('Diagram');
    $page->assertSee('Child Permissions');
    $page->assertSee('Permitted Users');
});

test('View Child Permissions', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::itemView->getItemName())
    ));
    $page->press('Child Permissions');
    $page->assertSee('Manage Child Permissions');
});

test('View Permitted Users', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::itemView->getItemName())
    ));
    $page->press('Permitted Users');
    $page->assertSee('User');
    $page->assertSee('Robert Peel');
    $page->assertSee('Granted By');
    $page->assertSee(Role::itemManager->getItemName());
});