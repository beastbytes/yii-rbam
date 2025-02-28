<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RolesCest
{
    public function initialRoles(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test Roles on initialisation');
        $I->expectTo('See that there are no Roles and a button to create a new Role');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.roles .btn']);
        $I->seeInCurrentUrl('/rbam/roles');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Roles', '.grid-view .header');
        $I->see('Name', '.grid-view .grid thead');
        $I->see('Description', '.grid-view .grid thead');
        $I->see('Created', '.grid-view .grid thead');
        $I->see('Updated', '.grid-view .grid thead');
        $I->see('Actions', '.grid-view .grid thead');
        $I->see('No roles found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}