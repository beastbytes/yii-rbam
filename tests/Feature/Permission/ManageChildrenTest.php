<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use Tests\Support\ActionButton;
use Tests\Support\Tab;
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
    $page->press($this->tab(Tab::childPermissions));
    $page->assertDontSeeIn($this->gridBody('permission'), 'A New Permission');
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('orphans', 1, 1), 'A New Permission');

    $page->press($this->actionButton('orphans', 1, ActionButton::add));
    $page->press('Continue');
    $page->assertSeeIn($this->gridCell('children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('orphans'), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press($this->tab(Tab::childPermissions));
    $page->assertSeeIn($this->gridCell('permission', 1, 1), 'A New Permission');
});

test('Remove Child Permission', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press($this->tab(Tab::childPermissions));
    $page->assertSeeIn($this->gridCell('permission', 1, 1), 'A New Permission');
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertSeeIn($this->gridCell('children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('orphans'), 'A New Permission');

    $page->press($this->actionButton('children', 1, ActionButton::remove));
    $page->press('Continue');
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('orphans', 1, 1), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press($this->tab(Tab::childPermissions));
    $page->assertDontSeeIn($this->gridBody('children'), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press($this->tab(Tab::childPermissions));
    $page->assertDontSeeIn($this->gridBody('permission'), 'A New Permission');

    $page = visit('http://localhost:8000/rbam/permissions');
    $page->assertSee('A New Permission');
})
    ->depends('Add Child Permission')
;
