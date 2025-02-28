<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class PermissionsCest
{
    public function initialPermissions(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test Permissions on initialisation');
        $I->expectTo('See that there are no Permissions and a button to create a new Permission');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.permissions .btn']);
        $I->seeInCurrentUrl('/rbam/permissions');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Permissions', '.grid-view .header');
        $I->see('Name', '.grid-view .grid thead');
        $I->see('Description', '.grid-view .grid thead');
        $I->see('Created', '.grid-view .grid thead');
        $I->see('Updated', '.grid-view .grid thead');
        $I->see('Actions', '.grid-view .grid thead');
        $I->see('No permissions found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}