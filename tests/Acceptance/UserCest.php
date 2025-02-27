<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class UserCest
{
    public function usersIndex(AcceptanceTester $I)
    {
        $I->amOnPage('/rbam');
        $I->click(['css' => '.users .btn']);
        $I->seeInCurrentUrl('/rbam/users');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Users', '.grid-view .header');
    }
}