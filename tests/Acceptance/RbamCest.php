<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RbamCest
{
    public function rbamIndex(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test the home page on initialisation');
        $I->expectTo('See 4 cards; one each for Roles, Permissions, Rules, and Users');
        $I->expect('Each card to describe what it is showing, have a count of and a button to manage the item');

        $I->amOnPage('/rbam');
        $I->see('Role Based Access Manager', 'h1');

        $I->seeNumberOfElements('.card', 4);

        $I->seeElement('.card.permissions');
        $I->see('Permissions', '.card.permissions .card-header');
        $I->see('0', '.card.permissions .badge');
        $I->see('Manage Permissions', '.card.permissions .btn_manage');

        $I->seeElement('.card.roles');
        $I->see('Roles', '.card.roles .card-header');
        $I->see('0', '.card.roles .badge');
        $I->see('Manage Roles', '.card.roles .btn_manage');

        $I->seeElement('.card.rules');
        $I->see('Rules', '.card.rules .card-header');
        $I->see('0', '.card.rules .badge');
        $I->see('Manage Rules', '.card.rules .btn_manage');

        $I->seeElement('.card.users');
        $I->see('Users', '.card.users .card-header');
        $I->see('51', '.card.users .badge');
        $I->see('Manage Users', '.card.users .btn_manage');
    }
}