<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Add Child Permission', function () {
    $page = visit('http://localhost:8000/rbam/permission/create');
    $page->type('#itemform-name', 'A New Permission');
    $page->type('#itemform-description', 'Description of a new permission');
    $page->press('Submit');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press('.tabs button.header:nth-child(2)'); // Child Permissions
    $page->assertDontSeeIn('#permission .grid tbody', 'A New Permission');
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertDontSeeIn('#children', 'A New Permission');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');

    $page->press('#orphans > .grid > tbody > tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
    $page->assertDontSeeIn('#orphans', 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press('.tabs button.header:nth-child(2)'); // Child Permissions
    $page->assertSeeIn('#permission .grid tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
});

test('Remove Child Permission', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press('.tabs button.header:nth-child(2)');
    $page->assertSeeIn(
        '#permission .grid > tbody > tr:nth-child(1) > td:nth-child(1)',
        'A New Permission'
    );
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertSeeIn('#children > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');
    $page->assertDontSeeIn('#orphans', 'A New Permission');

    $page->press('#children > .grid > tbody > tr:nth-child(1) button:nth-child(1)');
    $page->press('Continue');
    $page->assertDontSeeIn('#children', 'A New Permission');
    $page->assertSeeIn('#orphans > .grid > tbody > tr:nth-child(1) > td:nth-child(1)', 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press('.tabs button.header:nth-child(2)'); // Child Permissions
    $page->assertDontSeeIn('#children', 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press('.tabs button.header:nth-child(2)');
    $page->assertDontSeeIn('#permission .grid tbody', 'A New Permission');

    $page = visit('http://localhost:8000/rbam/permissions');
    $page->assertSee('A New Permission');
})
    ->depends('Add Child Permission')
;
