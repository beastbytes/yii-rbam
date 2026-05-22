<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\Rbac\Role;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('View a Role', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/role/%s',
        rawurlencode(Role::admin->getItemName())
    ));
    $page->assertSee('Name');
    $page->assertSee(Role::admin->getItemName());
    $page->assertSee('Description');
    $page->assertSee('Perform all RBAM functions');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');

    $page->assertSee('Diagram');
    $page->assertSee('Assignments');
    $page->assertSee('Child Roles');
    $page->assertSee('Permissions');
});

test('View Assignments', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode(Role::admin->getItemName())));
    $page->press('Assignments');
    $page->assertSee($this->getUserName(TestCase::CURRENT_USER));
});

test('View Child Roles', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode(Role::admin->getItemName())));
    $page->press('Child Roles');
    $page->assertSee('Manage Child Roles');
    $page->assertSee(Role::itemManager->getItemName());
    $page->assertSee(Role::ruleManager->getItemName());
    $page->assertSee(Role::userManager->getItemName());
});

test('View Permissions', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode(Role::admin->getItemName())));
    $page->press('Permissions');
    $page->assertSee('Manage Permissions');
    $page->assertSee('Page 1 of 2');
    $page->assertSee(Permission::index->getItemName());
});