<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\Rbac\Role;
use Tests\Support\ActionButton;
use Tests\TestCase;

afterAll(function () {
    TestCase::afterAll();
});

const USER_ID = 44; // Ensure not equal to TestCase::CURRENT_USER

test('Index', function () {
    $page = visit('http://localhost:8000/rbam');
    $page->click('Manage Users');

    $page->assertPathEndsWith('/rbam/users');
    $page->assertSee('Users');
    $page->assertSee('Name');
    $page->assertSee('Actions');
    $page->assertSee('Robert Walpole');

    $page->assertDontSeeIn(userName(TestCase::CURRENT_USER), $this->getUserName(TestCase::CURRENT_USER));
    $page->assertDontSeeIn(userRoleCount(TestCase::CURRENT_USER), (string) count(Role::cases()));
    $page->assertDontSeeIn(userPermissionCount(TestCase::CURRENT_USER), (string) count(Permission::cases()));

    $page->click($this->paginatorPage('users', 2));
    $page->assertSeeIn(userName(TestCase::CURRENT_USER), $this->getUserName(TestCase::CURRENT_USER));
    $page->assertSeeIn(userRoleCount(TestCase::CURRENT_USER), (string) count(Role::cases()));
    $page->assertSeeIn(userPermissionCount(TestCase::CURRENT_USER), (string) count(Permission::cases()));

    $page->click($this->paginatorPage('users', 3));
    $page->assertSee('Harold Wilson');

    $page->assertDontSeeIn(userName(TestCase::CURRENT_USER), $this->getUserName(TestCase::CURRENT_USER));
    $page->assertDontSeeIn(userRoleCount(TestCase::CURRENT_USER), (string) count(Role::cases()));
    $page->assertDontSeeIn(userPermissionCount(TestCase::CURRENT_USER), (string) count(Permission::cases()));
});

test('View User', function () {
    $page = visit('http://localhost:8000/rbam/users');
    $page->click($this->paginatorPage('users', userPage(TestCase::CURRENT_USER)));
    $page->press($this->ActionButton(
        'users',
        TestCase::CURRENT_USER % TestCase::PAGE_SIZE,
        ActionButton::view
    ));

    $page->assertPathEndsWith(sprintf('/rbam/user/%s', TestCase::CURRENT_USER));
    $page->assertSee($this->getUserName(TestCase::CURRENT_USER));
    $page->assertSee('Assigned Roles');
    $page->assertSeeIn($this->gridBody('assigned-roles'), Role::admin->getItemName());
    $page->assertSee('Unassigned Roles');
    $page->assertSee('Permissions Granted');
    $page->assertSeeIn($this->gridBody('permission'), Permission::index->getItemName());
});

test('Assign Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/user/%s', USER_ID));

    $page->assertDontSeeIn($this->gridBody('assigned-roles '), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('unassigned-roles'), Role::itemManager->getItemName());
    $page->assertDontSeeIn($this->gridBody('permission'), 'RBAM Permission View');

    $page->press($this->actionButton('unassigned-roles', 2, ActionButton::assign));
    $page->press('Continue');

    $page->assertSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertDontSeeIn($this->gridBody('unassigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('permission'), Permission::itemView->getItemName());

    // See the permission and role count in list of users
    $page->click(usersBreadcrumb());
    $page->click($this->paginatorPage('users', userPage(USER_ID)));
    $page->assertSeeIn(userRoleCount(USER_ID), '1');
    $page->assertSeeIn(userPermissionCount(USER_ID), '5');

    // See the assignment and permissions on the view role page
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', Role::itemManager->getItemName()));
    $page->press('Assignments');
    $page->assertSeeIn($this->gridBody('assignments'), $this->getUserName(USER_ID));
    $page->press('Permissions');
    $page->assertSeeIn($this->gridBody('permission'), Permission::itemView->getItemName());

    // See the permission permitted user on the view permission page
    $page = visit(sprintf('http://localhost:8000/rbam/permission/%s', Permission::itemView->getItemName()));
    $page->press('Permitted Users');
    $page->assertSeeIn($this->gridBody('permitted-users'), $this->getUserName(USER_ID));
});

test('Revoke Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/user/%s', USER_ID));

    $page->assertSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('assigned-roles'), 'Revoke');
    $page->assertDontSeeIn($this->gridBody('unassigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('permission'), Permission::itemView->getItemName());

    $page->press($this->actionButton('assigned-roles', 1, ActionButton::revoke));
    $page->press('Continue');

    $page->assertDontSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('unassigned-roles'), Role::itemManager->getItemName());
    $page->assertDontSeeIn($this->gridBody('permission'), Permission::itemView->getItemName());

    $page->click(usersBreadcrumb()); // Users
    $page->click($this->paginatorPage('users', userPage(USER_ID)));
    $page->assertSeeIn(userRoleCount(USER_ID), '0');
    $page->assertSeeIn(userPermissionCount(USER_ID), '0');
})
    ->depends('Assign Role')
;

test('Revoke All Roles', function () {
    $page = visit('http://localhost:8000/rbam/user/' . USER_ID);

    // Assign a couple of roles
    $page->assertDontSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertDontSeeIn($this->gridBody('assigned-roles'), Role::ruleManager->getItemName());

    $page->press($this->actionButton('unassigned-roles', 2, ActionButton::assign));
    $page->press('Continue');
    $page->press($this->actionButton('unassigned-roles', 2, ActionButton::assign));
    $page->press('Continue');

    $page->assertSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('assigned-roles'), Role::ruleManager->getItemName());

    $page->press('Revoke All');
    $page->press('Continue');

    $page->assertDontSeeIn($this->gridBody('assigned-roles'), Role::itemManager->getItemName());
    $page->assertDontSeeIn($this->gridBody('assigned-roles'), Role::ruleManager->getItemName());
    $page->assertSeeIn($this->gridBody('unassigned-roles'), Role::itemManager->getItemName());
    $page->assertSeeIn($this->gridBody('unassigned-roles'), Role::ruleManager->getItemName());
})
    ->depends('Revoke Role')
;

//------ Helper functions ------//
function userName(int $id): string
{
    return sprintf('%s > td:nth-child(1)', userRow($id));
}

function userPage(int $id): int
{
    return $id / TestCase::PAGE_SIZE + ($id % TestCase::PAGE_SIZE ? 1 : 0);
}

function userPermissionCount(int $id): string
{
    return sprintf('%s > td:nth-child(3)', userRow($id));
}
function userRoleCount(int $id): string
{
    return sprintf('%s > td:nth-child(2)', userRow($id));
}

function userRow(int $id): string
{
    return sprintf('#users .grid > tbody > tr:nth-child(%d)', $id % TestCase::PAGE_SIZE);
}

function usersBreadcrumb(): string
{
    return '.breadcrumb > li:nth-child(3) > a:nth-child(1)';
}