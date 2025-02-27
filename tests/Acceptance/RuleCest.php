<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RuleCest
{
    public function rulesIndex(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.rules .btn']);
        $I->seeInCurrentUrl('/rbam/rules');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Rules', '.grid-view .header');
        $I->see('No rules found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}