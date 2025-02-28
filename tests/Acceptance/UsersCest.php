<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class UsersCest
{
    public function initialUsers(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test Users on initialisation');
        $I->expectTo('See that there are Users and that none have been assigned any roles and therefore not granted any permissions');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.users .btn']);
        $I->seeInCurrentUrl('/rbam/users');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Users', '.grid-view .header');
        $I->see('Name', '.grid-view .grid thead');
        $I->see('Roles', '.grid-view .grid thead');
        $I->see('Permissions', '.grid-view .grid thead');
        $I->see('Actions', '.grid-view .grid thead');

        for ($i = 1; $i <= 20; $i++) {
            $I->seeUserHasName($I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[1]"));
            $I->seeUserHasPermissions(' ', $I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[2]"));
            $I->seeUserHasRoles( ' ', $I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[2]"));
        }
    }
}