<?php

use Tests\Support\ActionButton;
use Tests\Support\RoleTab;
use Tests\Support\Tab;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Add Permission', function () {
    $page = visit('http://localhost:8000/rbam/permission/create');
    $page->type('#itemform-name', 'A New Permission');
    $page->type('#itemform-description', 'Description of new permission');
    $page->press('Submit');

    $page = visit('http://localhost:8000/rbam/role/create');
    $page->type('#itemform-name', 'A New Role');
    $page->type('#itemform-description', 'A new role');
    $page->press('Submit');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->press($this->tab(Tab::permissions));
    $page->assertDontSeeIn($this->gridBody('permission'), 'A New Permission');
    $page->assertSeeLink('Manage Permissions');
    $page->click('Manage Permissions');

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/permission', rawurlencode('A New Role')));
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('orphans', 1, 1), 'A New Permission');

    $page->press($this->actionButton('orphans', 1, ActionButton::add));
    $page->press('Continue');
    $page->assertSeeIn($this->gridCell('children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('orphans'), 'A New Permission');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->press($this->tab(Tab::permissions));
    $page->assertSeeIn($this->gridCell('permission', 1, 1), 'A New Permission');
});

test('Add Child Role', function () {
    $page = visit('http://localhost:8000/rbam/role/create');
    $page->type('#itemform-name', 'Another New Role');
    $page->type('#itemform-description', 'Description of another new role');
    $page->press('Submit');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press($this->tab(Tab::childRoles));
    $page->assertDontSeeIn($this->gridBody('role'), 'A New Role');
    $page->assertSeeLink('Manage Child Roles');
    $page->click('Manage Child Roles');

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/role', rawurlencode('Another New Role')));
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Role');
    $page->assertSeeIn($this->gridCell('orphans', 1, 1), 'A New Role');

    $page->press($this->actionButton('orphans', 1, ActionButton::add));
    $page->press('Continue');
    $page->assertSeeIn($this->gridCell('#children', 1, 1), 'A New Role');
    $page->assertDontSeeIn($this->gridBody('#orphans'), 'A New Role');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press($this->tab(Tab::childRoles));
    $page->assertSeeIn($this->gridCell('role', 1, 1), 'A New Role');
})
    ->depends('Add Permission')
;

test('Remove Child Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press($this->tab(Tab::childRoles));
    $page->assertSeeLink('Manage Child Roles');
    $page->click('Manage Child Roles'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/role', rawurlencode('Another New Role')));
    $page->assertSeeIn($this->gridCell('children', 1, 1), 'A New Role');
    $page->assertDontSeeIn($this->gridBody('orphans'), 'A New Role');

    $page->press($this->actionButton('children', 1, ActionButton::remove));
    $page->press('Continue');
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Role');
    $page->assertSeeIn($this->gridCell('orphans', 1,1), 'A New Role');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press($this->tab(Tab::childRoles));
    $page->assertDontSeeIn($this->gridBody('role'), 'A New Role');

    $page = visit('http://localhost:8000/rbam/roles');
    $page->assertSee('A New Role');
})
    ->depends('Add Child Role')
;

test('Remove Permission', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->press($this->tab(Tab::permissions));
    $page->assertSeeLink('Manage Permissions');
    $page->click('Manage Permissions'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/permission', rawurlencode('A New Role')));
    $page->assertSeeIn($this->gridCell('children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('orphans'), 'A New Permission');

    $page->press($this->actionButton('children', 1, ActionButton::remove));
    $page->press('Continue');
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('orphans', 1, 1), 'A New Permission');

    $page = visit('http://localhost:8000/rbam/permissions');
    $page->assertSee('A New Permission');
})
    ->depends('Add Permission')
;