<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class PermissionCest
{
    public function permissionsIndex(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.permissions .btn']);
        $I->seeInCurrentUrl('/rbam/permissions');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Permissions', '.grid-view .header');
        $I->see('No permissions found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}