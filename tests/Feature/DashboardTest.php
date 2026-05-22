<?php

use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

test('Dashboard', function () {
    $page = visit('http://localhost:8000/rbam');
    $page->assertSee('Role Based Access Manager');
    $page->assertSee('Roles');
    $page->assertSee('Permissions');
    $page->assertSee('Rules');
    $page->assertSee('Users');
    $page->assertSeeLink('Manage Roles');
    $page->assertSeeLink('Manage Permissions');
    $page->assertSeeLink('Manage Rules');
    $page->assertSeeLink('Manage Users');
});
