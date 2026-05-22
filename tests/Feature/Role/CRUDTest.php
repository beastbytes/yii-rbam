<?php

use BeastBytes\Yii\Rbam\Rbac\Role;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Index', function () {
    $page = visit('http://localhost:8000/rbam');
    $page->click('Manage Roles');

    $page->assertPathEndsWith('/roles');
    $page->assertSee('Roles');
    $page->assertSeeLink('Create Role');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');
    $page->assertSee('Actions');
    $page->assertSee(Role::admin->getItemName());
});

test('Create', function () {
    $page = visit('http://localhost:8000/rbam/roles');
    $page->click('Create Role');
    $page->assertPathEndsWith('/rbam/role/create');

    $page->assertSee('Create Role');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');

    $page->type('#itemform-name', 'A New Role');
    $page->type('#itemform-description', 'A new role');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/role/%s', rawurlencode('A New Role')));
    $page->assertSee('A New Role');
    $page->assertSee('A new role');
    $page->assertSee('Diagram');
    $page->assertSee('Assignments');
    $page->assertSee('Child Roles');
    $page->assertSee('Permissions');

    $page->click('Roles');
    $page->assertSee('A New Role');
    $page->assertSee('A new role');

});

test('Duplicate Not Allowed', function () {
    $page = visit('http://localhost:8000/rbam/role/create');

    $page->type('#itemform-name', 'A New Role');
    $page->type('#itemform-description', 'A new role');
    $page->press('Submit');

    $page->assertPathEndsWith('/rbam/role/create');
    $page->assertSee('A New Role already exists');
})
    ->depends('Create');
;

test('Update', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->click('Update');
    $page->assertPathEndsWith(sprintf('/rbam/role/%s/update', rawurlencode('A New Role')));

    $page->type('#itemform-name', 'A New RBAC Role');
    $page->type('#itemform-description', 'A new RBAC role');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/role/%s', rawurlencode('A New RBAC Role')));
    $page->assertSee('A New RBAC Role');
    $page->assertSee('A new RBAC role');

    $page->click('Roles');
    $page->assertSee('A New RBAC Role');
    $page->assertSee('A new RBAC role');
    $page->assertDontSee('A New Role');
    $page->assertDontSee('A new role');
})
    ->depends('Create');
;

test('Delete', function () {
    $page = visit('http://localhost:8000/rbam/roles');

    $page->assertSee('A New RBAC Role');

    $page->click('.grid tr:nth-child(1) button:nth-child(3)');
    $page->assertSee('Remove A New RBAC Role Role');
    $page->click('Continue');

    $page->assertSee('Roles');
    $page->assertSeeLink('Create Role');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Rule');
    $page->assertSee('Created');
    $page->assertSee('Updated');

    $page->assertDontSee('A New RBAC Role');
})
    ->depends('Update');
;

test('Create with Rule', function () {
    $page = visit('http://localhost:8000/rbam/role/create');

    $page->type('#itemform-name', 'A New Role');
    $page->type('#itemform-description', 'A new role');
    $page->select('#itemform-rulename', 'False');
    $page->press('Submit');

    $page->assertPathEndsWith(sprintf('/rbam/role/%s', rawurlencode('A New Role')));
    $page->assertSee('A New Role');
    $page->assertSee('A new role');
    $page->assertSee('False');
})
    ->depends('Delete');
;