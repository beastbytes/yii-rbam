<?php

namespace Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RbamControllerCest
{
    public function rbamHomePage(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Permissions', '.card.permissions');
        $I->see('Roles', '.card.roles');
        $I->see('Rules', '.card.rules');
        $I->see('Users', '.card.users');
        $I->see('0', '.card.permissions .badge');
        $I->see('0', '.card.roles .badge');
        $I->see('0', '.card.rules .badge');
        $I->see('51', '.card.users .badge');
        $I->see('Manage Permissions', '.card.permissions .btn');
        $I->see('Manage Roles', '.card.roles .btn');
        $I->see('Manage Rules', '.card.rules .btn');
        $I->see('Manage Users', '.card.users .btn');
    }

    public function rbamHomePageToPermissions(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.permissions .btn']);
        $I->seeInCurrentUrl('/rbam/permissions');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Permissions', '.grid-view .header');
        $I->see('No permissions found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    public function rbamHomePageToRoles(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.roles .btn']);
        $I->seeInCurrentUrl('/rbam/roles');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Roles', '.grid-view .header');
        $I->see('No roles found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    public function rbamHomePageToRules(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.rules .btn']);
        $I->seeInCurrentUrl('/rbam/rules');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Rules', '.grid-view .header');
        $I->see('No rules found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    public function rbamHomePageToUsers(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.users .btn']);
        $I->seeInCurrentUrl('/rbam/users');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Users', '.grid-view .header');
    }
}