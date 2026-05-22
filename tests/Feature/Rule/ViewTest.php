<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\Rbac\Role;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('View a Rule', function () {
    $page = visit('http://localhost:8000/rbam/rule/False');
    $page->assertSee('Name');
    $page->assertSee('False');
    $page->assertSee('Description');
    $page->assertSee('Always returns FALSE');
    $page->assertSee('Code');
    $page->assertSee('public function execute(?string $userId, Permission $item, RuleContext $context): bool');

    $page->assertSee('Roles');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');
    $page->assertSee('No roles found');

    $page->assertSee('Permissions');
    $page->press('Permissions');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Granted By');
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');
    $page->assertSee('No permissions found');
});

test('View a Rule applied to a Role', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/role/%s/update',
        rawurlencode(Role::itemManager->getItemName())
    ));
    $page->select('#itemform-rulename', 'True');
    $page->press('Submit');

    $page = visit('http://localhost:8000/rbam/rule/True');

    $page->assertSee('Roles');
    $page->assertSee('Name');
    $page->assertSee(Role::itemManager->getItemName());
    $page->assertSee('Description');
    $page->assertSee('Create, update, delete, and view RBAC Roles and Permissions');
    $page->assertSee('Rule');
    $page->assertSee('True');
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');

    $page->press('Permissions');
    $page->assertSee('No permissions found');
});

test('View a Rule applied to a Permission', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s/update',
        rawurlencode(Permission::itemCreate->getItemName())
    ));
    $page->select('#itemform-rulename', 'True');
    $page->press('Submit');

    $page = visit('http://localhost:8000/rbam/rule/True');

    $page->press('Permissions');
    $page->assertSee('Name');
    $page->assertSee(Permission::itemCreate->getItemName());
    $page->assertSee('Description');
    $page->assertSee('Create Permissions and Roles');
    $page->assertSee('Rule');
    $page->assertSee('True');
    $page->assertSee('Granted By');
    $page->assertSee(Role::itemManager->getItemName());
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');
});