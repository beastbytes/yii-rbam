<?php

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
    $page->press('.tabs button.header:nth-child(3)'); // Permissions
    $page->assertDontSeeIn('#permission .grid tbody', 'A New Permission');
    $page->assertSeeLink('Manage Permissions');
    $page->click('Manage Permissions'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/permission', rawurlencode('A New Role')));
    $page->assertDontSeeIn('#children', 'A New Permission');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');

    $page->press('#orphans > .grid > tbody > tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
    $page->assertDontSeeIn('#orphans', 'A New Permission');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->press('.tabs button.header:nth-child(3)'); // Permissions
    $page->assertSeeIn('#permission .grid tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
});

test('Add Child Role', function () {
    $page = visit('http://localhost:8000/rbam/role/create');
    $page->type('#itemform-name', 'Another New Role');
    $page->type('#itemform-description', 'Description of another new role');
    $page->press('Submit');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press('.tabs button.header:nth-child(2)'); // Child Roles
    $page->assertDontSeeIn('#role .grid tbody', 'A New Role');
    $page->assertSeeLink('Manage Child Roles');
    $page->click('Manage Child Roles'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/role', rawurlencode('Another New Role')));
    $page->assertDontSeeIn('#children', 'A New Role');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Role');

    $page->press('#orphans .grid tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Role');
    $page->assertDontSeeIn('#orphans', 'A New Role');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press('.tabs button.header:nth-child(2)'); // Child Roles
    $page->assertSeeIn('#role .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Role');
})
    ->depends('Add Permission')
;

test('Remove Child Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press('.tabs button.header:nth-child(2)'); // Child Roles
    $page->assertSeeLink('Manage Child Roles');
    $page->click('Manage Child Roles'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/role', rawurlencode('Another New Role')));
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Role');
    $page->assertDontSeeIn('#orphans', 'A New Role');

    $page->press('#children > .grid > tbody > tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertDontSeeIn('#children', 'A New Role');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Role');

    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('Another New Role')));
    $page->press('.tabs button.header:nth-child(2)'); // Child Roles
    $page->assertDontSeeIn('#role .grid tbody', 'A New Role');

    $page = visit('http://localhost:8000/rbam/roles');
    $page->assertSee('A New Role');
})
    ->depends('Add Child Role')
;

test('Remove Permission', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', rawurlencode('A New Role')));
    $page->press('.tabs button.header:nth-child(3)'); // Permissions
    $page->assertSeeLink('Manage Permissions');
    $page->click('Manage Permissions'); // Manage button

    $page->assertPathEndsWith(sprintf('role/%s/manage-children/permission', rawurlencode('A New Role')));
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
    $page->assertDontSeeIn('#orphans', 'A New Permission');

    $page->press('#children > .grid > tbody > tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertDontSeeIn('#children', 'A New Permission');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');

    $page = visit('http://localhost:8000/rbam/permissions');
    $page->assertSee('A New Permission');
})
    ->depends('Add Permission')
;