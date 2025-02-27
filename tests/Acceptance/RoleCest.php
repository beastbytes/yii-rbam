<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RoleCest
{
    public function rolesIndex(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.roles .btn']);
        $I->seeInCurrentUrl('/rbam/roles');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Roles', '.grid-view .header');
        $I->see('No roles found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}