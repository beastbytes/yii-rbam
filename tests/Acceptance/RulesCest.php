<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;

class RulesCest
{
    public function initialRules(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test Rules on initialisation');
        $I->expectTo('See that there are no Rules and a button to create a new Rule');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.rules .btn']);
        $I->seeInCurrentUrl('/rbam/rules');
        $I->see('Role Based Access Manager', 'h1');
        $I->see('Rules', '.grid-view .header');
        $I->see('Name', '.grid-view .grid thead');
        $I->see('Description', '.grid-view .grid thead');
        $I->see('Actions', '.grid-view .grid thead');
        $I->see('No rules found', '.grid-view .grid tbody');
        $I->seeElement('.grid-view .toolbar .btn_create');
    }
}