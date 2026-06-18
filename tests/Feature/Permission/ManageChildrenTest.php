<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use Tests\Support\ChildActionButton;
use Tests\Support\OrphanActionButton;
use Tests\Support\PermissionTab;
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
    $page->assertDontSeeIn($this->gridBody('#permission'), 'A New Permission');
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertDontSeeIn($this->gridBody('#children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('#orphans', 1, 1), 'A New Permission');

    $page->press($this->actonButton('#orphans', 1, OrphanActionButton::add));
    $page->press('Continue');
    $page->assertSeeIn($this->gridCell('#children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('#orphans'), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press($this->tab(PermissionTab::childPermissions));
    $page->assertSeeIn($this->gridCell('#permission', 1, 1), 'A New Permission');
});

test('Remove Child Permission', function () {
    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press($this->tab(PermissionTab::childPermissions));
    $page->assertSeeIn($this->gridCell('#permission', 1, 1), 'A New Permission');
    $page->assertSeeLink('Manage Child Permissions');
    $page->click('Manage Child Permissions');

    $page->assertPathEndsWith(sprintf(
        'permission/%s/manage-children/permission',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->assertSeeIn($this->gridCell('#children', 1, 1), 'A New Permission');
    $page->assertDontSeeIn($this->gridBody('#orphans'), 'A New Permission');

    $page->press($this->actionButton('#children', 1, ChildActionButton::remove));
    $page->press('Continue');
    $page->assertDontSeeIn($this->gridBody('#children'), 'A New Permission');
    $page->assertSeeIn($this->gridCell('#orphans', 1, 1), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(Permission::index->getItemName())
    ));
    $page->press($this->tab(PermissionTab::childPermissions));
    $page->assertDontSeeIn($this->gridBody('#children'), 'A New Permission');

    $page = visit(sprintf(
        'http://localhost:8000/rbam/permission/%s',
        rawurlencode(rawurlencode(Permission::index->getItemName()))
    ));
    $page->press($this->tab(PermissionTab::childPermissions));
    $page->assertDontSeeIn($this->gridBody('#permission'), 'A New Permission');

    $page = visit('http://localhost:8000/rbam/permissions');
    $page->assertSee('A New Permission');
})
    ->depends('Add Child Permission')
;
