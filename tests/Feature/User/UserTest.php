<?php

use BeastBytes\Yii\Rbam\Rbac\Permission;
use BeastBytes\Yii\Rbam\Rbac\Role;
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

    $page->click(paginatorPage(2));
    $page->assertSeeIn(userName(TestCase::CURRENT_USER), $this->getUserName(TestCase::CURRENT_USER));
    $page->assertSeeIn(userRoleCount(TestCase::CURRENT_USER), (string) count(Role::cases()));
    $page->assertSeeIn(userPermissionCount(TestCase::CURRENT_USER), (string) count(Permission::cases()));

    $page->click(paginatorPage(3));
    $page->assertSee('Harold Wilson');

    $page->assertDontSeeIn(userName(TestCase::CURRENT_USER), $this->getUserName(TestCase::CURRENT_USER));
    $page->assertDontSeeIn(userRoleCount(TestCase::CURRENT_USER), (string) count(Role::cases()));
    $page->assertDontSeeIn(userPermissionCount(TestCase::CURRENT_USER), (string) count(Permission::cases()));
});

test('View User', function () {
    $page = visit('http://localhost:8000/rbam/users');
    $page->click(paginatorPage(userPage(TestCase::CURRENT_USER)));
    $page->press(userViewButton(TestCase::CURRENT_USER));

    $page->assertPathEndsWith(sprintf('/rbam/user/%s', TestCase::CURRENT_USER));
    $page->assertSee($this->getUserName(TestCase::CURRENT_USER));
    $page->assertSee('Assigned Roles');
    $page->assertSeeIn('#assigned-roles .grid', Role::admin->getItemName());
    $page->assertSee('Unassigned Roles');
    $page->assertSee('Permissions Granted');
    $page->assertSeeIn('#permission .grid', Permission::index->getItemName());
});

test('Assign Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/user/%s', USER_ID));

    $page->assertDontSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#unassigned-roles .grid', Role::itemManager->getItemName());
    $page->assertDontSeeIn('#permission .grid', 'RBAM Permission View');

    $page->press(assignButton());
    $page->press('Continue');

    $page->assertSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertDontSeeIn('#unassigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#permission .grid', Permission::itemView->getItemName());

    // See the permission and role count in list of users
    $page->click(usersBreadcrumb());
    $page->click(paginatorPage(userPage(USER_ID)));
    $page->assertSeeIn(userRoleCount(USER_ID), '1');
    $page->assertSeeIn(userPermissionCount(USER_ID), '5');

    // See the assignment and permissions on the view role page
    $page = visit(sprintf('http://localhost:8000/rbam/role/%s', Role::itemManager->getItemName()));
    $page->press('Assignments');
    $page->assertSeeIn('#assignments .grid', $this->getUserName(USER_ID));
    $page->press('Permissions');
    $page->assertSeeIn('#permission .grid', Permission::itemView->getItemName());

    // See the permission permitted user on the view permission page
    $page = visit(sprintf('http://localhost:8000/rbam/permission/%s', Permission::itemView->getItemName()));
    $page->press('Permitted Users');
    $page->assertSeeIn('#permitted-users .grid', $this->getUserName(USER_ID));
});

test('Revoke Role', function () {
    $page = visit(sprintf('http://localhost:8000/rbam/user/%s', USER_ID));

    $page->assertSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#assigned-roles .grid', 'Revoke');
    $page->assertDontSeeIn('#unassigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#permission .grid', Permission::itemView->getItemName());

    $page->press(revokeButton());
    $page->press('Continue');

    $page->assertDontSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#unassigned-roles .grid', Role::itemManager->getItemName());
    $page->assertDontSeeIn('#permission .grid', Permission::itemView->getItemName());

    $page->click(usersBreadcrumb()); // Users
    $page->click(paginatorPage(userPage(USER_ID)));
    $page->assertSeeIn(userRoleCount(USER_ID), '0');
    $page->assertSeeIn(userPermissionCount(USER_ID), '0');
})
    ->depends('Assign Role')
;

test('Revoke All Roles', function () {
    $page = visit('http://localhost:8000/rbam/user/' . USER_ID);

    // Assign a couple of roles
    $page->assertDontSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertDontSeeIn('#assigned-roles .grid', Role::ruleManager->getItemName());

    $page->press(assignButton());
    $page->press('Continue');
    $page->press(assignButton());
    $page->press('Continue');

    $page->assertSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#assigned-roles .grid', Role::ruleManager->getItemName());

    $page->press('Revoke All');
    $page->press('Continue');

    $page->assertDontSeeIn('#assigned-roles .grid', Role::itemManager->getItemName());
    $page->assertDontSeeIn('#assigned-roles .grid', Role::ruleManager->getItemName());
    $page->assertSeeIn('#unassigned-roles .grid', Role::itemManager->getItemName());
    $page->assertSeeIn('#unassigned-roles .grid', Role::ruleManager->getItemName());
})
    ->depends('Revoke Role')
;

// Helper functions

function assignButton(): string
{
    return '#unassigned-roles > .grid > tbody > tr:nth-child(2) > td:nth-child(3) > .btn_assign';
}

/* String options are 'first', 'prev' or 'previous', 'next', and 'last' */
function paginatorPage(int|string $page): string
{
    if (is_string($page)) {
        return match ($page) {
            'first' => '#users > nav:nth-child(4) > a:nth-child(1)',
            'last' => '#users > nav:nth-child(4) > a:nth-last-child(1)',
            'next' => '#users > nav:nth-child(4) > a:nth-last-child(2)',
            'prev', 'previous' => '#users > nav:nth-child(4) > a:nth-child(2)',
        };
    }

    return sprintf('#users > nav:nth-child(4) > a:nth-child(%d)', $page + 2);
}

function revokeAllButton(): string
{
    return '#assigned-roles > .toolbar > .btn_revoke-all';
}

function revokeButton(): string
{
    return '#assigned-roles > .grid > tbody > tr:nth-child(1) > td:nth-child(3) > .btn_revoke';
}

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

function userViewButton(int $id): string
{
    return sprintf('%s > td:nth-child(4) > .btn_view', userRow($id));
}

function usersBreadcrumb(): string
{
    return '.breadcrumb > li:nth-child(3) > a:nth-child(1)';
}