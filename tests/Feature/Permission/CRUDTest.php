<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use Tests\Support\ItemActionButton;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Index', function () {
    $page = visit('http://localhost:8000/rbam');
    $page->click('Manage Permissions');

    $page->assertPathEndsWith('/permissions');
    $page->assertSee('Permissions');
    $page->assertSeeLink('Create Permission');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');
    $page->assertSee(Permission::itemView->getItemName());
});

test('Create', function () {
    $page = visit('http://localhost:8000/rbam/permissions');
    $page->click('Create Permission');
    $page->assertPathEndsWith('/rbam/permission/create');

    $page->assertSee('Create Permission');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');

    $page->type('#itemform-name', 'A New Permission');
    $page->type('#itemform-description', 'A new permission');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/permission/%s', rawurlencode('A New Permission')));
    $page->assertSee('A New Permission');
    $page->assertSee('A new permission');
    $page->assertSee('Diagram');
    $page->assertSee('Permitted Users');

    $page->click('Permissions');
    $page->assertSee('A New Permission');
    $page->assertSee('A new permission');
});

test('Duplicate Not Allowed', function () {
    $page = visit('http://localhost:8000/rbam/permission/create');

    $page->type('#itemform-name', 'A New Permission');
    $page->type('#itemform-description', 'A new permission');
    $page->press('Submit');

    $page->assertPathEndsWith('/rbam/permission/create');
    $page->assertSee('A New Permission already exists');
})
    ->depends('Create');
;

test('Update', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/permission/%s', rawurlencode('A New Permission')));
    $page->click('Update');
    $page->assertPathEndsWith(sprintf('/rbam/permission/%s/update', rawurlencode('A New Permission')));

    $page->type('#itemform-name', 'A New RBAC Permission');
    $page->type('#itemform-description', 'A new RBAC permission');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/permission/%s', rawurlencode('A New RBAC Permission')));
    $page->assertSee('A New RBAC Permission');
    $page->assertSee('A new RBAC permission');

    $page->click('Permissions');
    $page->assertSee('A New RBAC Permission');
    $page->assertSee('A new RBAC permission');
    $page->assertDontSee('A New Permission');
    $page->assertDontSee('A new permission');
})
    ->depends('Create');
;

test('Delete', function () {
    $page = visit('http://localhost:8000/rbam/permissions');

    $page->assertSee('A New RBAC Permission');

    $page->click($this->actionButton('#permission', 1, ItemActionButton::remove));
    $page->assertSee('Remove A New RBAC Permission Permission');
    $page->click('Continue');

    $page->assertSee('Permissions');
    $page->assertSeeLink('Create Permission');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');

    $page->assertDontSee('A New RBAC Permission');
})
    ->depends('Update');
;

test('Create with Rule', function () {
    $page = visit('http://localhost:8000/rbam/permission/create');

    $page->type('#itemform-name', 'A New Permission');
    $page->type('#itemform-description', 'A new permission');
    $page->select('#itemform-rulename', 'False');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/permission/%s', rawurlencode('A New Permission')));
    $page->assertSee('A New Permission');
    $page->assertSee('A new permission');
    $page->assertSee('False');
})
    ->depends('Delete');
;