<?php

use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Index', function () {
    $page = visit('http://localhost:8000/rbam');
    $page->click('Manage Rules');

    $page->assertPathEndsWith('/rules');
    $page->assertSee('Rules');
    $page->assertSeeLink('Create Rule');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Actions');
    $page->assertSee('False');
});

test('Create', function () {
    $page = visit('http://localhost:8000/rbam/rules');
    $page->click('Create Rule');
    $page->assertPathEndsWith('/rbam/rule/create');

    $page->assertSee('Create Rule');
    $page->assertSee('Name');
    $page->assertSee('Description');

    $page->type('#createruleform-name', 'ANew');
    $page->type('#createruleform-description', 'A new rule');
    $page->type('#createruleform-code', 'return $userId > 1;');
    $page->press('Submit');

    $page->assertPathEndsWith('/rbam/rule/ANew');
    $page->assertSee('ANew');
    $page->assertSee('A new rule');
    $page->assertSee('return $userId > 1;');
});

test('Create Duplicate Error', function () {
    $page = visit('http://localhost:8000/rbam/rule/create');

    $page->type('#createruleform-name', 'ANew');
    $page->type('#createruleform-description', 'A new rule');
    $page->type('#createruleform-code', 'return $userId > 1;');
    $page->press('Submit');

    $page->assertPathEndsWith('/rbam/rule/create');
    $page->assertSee('ANew already exists');
})
    ->depends('Create');
;

test('Update', function () {
    $page = visit('http://localhost:8000/rbam/rule/ANew');
    $page->click('Update');
    $page->assertPathEndsWith('/rbam/rule/ANew/update');

    $page->type('#updateruleform-description', 'A new RBAC rule');
    $page->press('Submit');

    $page->assertPathEndsWith('/rbam/rule/ANew');
    $page->assertSee('ANew');
    $page->assertSee('A new RBAC rule');
    $page->assertSee('return $userId > 1;');
})
    ->depends('Create');
;

test('Delete', function () {
    $page = visit('http://localhost:8000/rbam/rules');

    $page->assertSee('ANew');

    $page->click('.grid tr:nth-child(1) button:nth-child(3)');
    $page->assertSee('Remove ANew Rule');
    $page->click('Continue');

    $page->assertSee('Rules');
    $page->assertSeeLink('Create Rule');
    $page->assertSee('Name');
    $page->assertSee('Description');
    $page->assertSee('Actions');

    $page->assertDontSee('ANew');
})
    ->depends('Update');
;