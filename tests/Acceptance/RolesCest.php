<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;
use Codeception\Attribute\Before;
use Codeception\Attribute\Depends;

class RolesCest
{
    #[Before('cleanup')]
    public function createRoles(AcceptanceTester $I): void
    {
        $I->amGoingTo('Create some Roles');
        $I->amOnPage('/rbam/roles');

        //------------------------

        $I->click('html/body/div[1]/div/div[2]/a');
        $I->see('Create Role', 'html/body/div[1]/h2');

        $I->fillField(['name' => 'ItemForm[name]'], 'PostManager');
        $I->fillField(['name' => 'ItemForm[description]'], 'Manage blog posts');
        $I->click('Submit');

        $I->see('PostManager Role', 'html/body/div[1]/div[1]/div');

        $I->click('html/body/div[1]/a');
        $I->see('PostManager', 'html/body/div[1]/div/table/tbody/tr[1]/td[1]');
        $I->see('Manage blog posts', 'html/body/div[1]/div/table/tbody/tr[1]/td[2]');

        //------------------------

        $I->click('html/body/div[1]/div/div[2]/a');
        $I->see('Create Role', 'html/body/div[1]/h2');

        $I->fillField(['name' => 'ItemForm[name]'], 'PostEditor');
        $I->fillField(['name' => 'ItemForm[description]'], 'Edit posts');
        $I->click('Submit');

        $I->see('PostEditor Role', 'html/body/div[1]/div[1]/div');

        $I->click('html/body/div[1]/a');
        $I->see('PostEditor', 'html/body/div[1]/div/table/tbody/tr[1]/td[1]');
        $I->see('Edit posts', 'html/body/div[1]/div/table/tbody/tr[1]/td[2]');
        $I->see('PostManager', 'html/body/div[1]/div/table/tbody/tr[2]/td[1]');
        $I->see('Manage blog posts', 'html/body/div[1]/div/table/tbody/tr[2]/td[2]');
    }

    #[Depends('createRoles')]
    public function updateRole(AcceptanceTester $I)
    {
        $I->amGoingTo('Update a Role');
        $I->amOnPage('/rbam/roles');

        $I->click('html/body/div[1]/div/table/tbody/tr[1]/td[5]/a[1]');
        $I->see('PostEditor Role', 'html/body/div[1]/div[1]/div');
        $I->click('html/body/div[1]/div[1]/a');
        $I->see('Update Role', 'html/body/div[1]/h2');
        $I->fillField(['name' => 'ItemForm[description]'], 'Edit blog posts');
        $I->click('Submit');

        $I->see('PostEditor Role', 'html/body/div[1]/div[1]/div');

        $I->click('html/body/div[1]/a');
        $I->see('PostEditor', 'html/body/div[1]/div/table/tbody/tr[1]/td[1]');
        $I->see('Edit blog posts', 'html/body/div[1]/div/table/tbody/tr[1]/td[2]');
    }

    #[Depends('updateRole')]
    public function createHierarchy(AcceptanceTester $I)
    {
        $I->amGoingTo('Create a Role Hierarchy');
        $I->amOnPage('/rbam/roles');

        $I->click('html/body/div[1]/div/table/tbody/tr[2]/td[5]/a[1]');
        $I->click('html/body/div[1]/div[2]/label[2]');
        $I->click('html/body/div[1]/div[2]/div[2]/div/div[2]/a');

        $I->see('Manage Child Roles for PostManager', 'html/body/div[1]/div/div[1]');
        //@todo needs web browser because assignment requires JavaScript
        $I->fillField('post_editor', 'PostEditor');
        $I->click('html/body/div[1]/a');

        $I->see('PostEditor', 'html/body/div[1]/div[2]/div[2]/div/table/tbody/tr/td[1]');
    }

    protected function cleanup()
    {
        $rbacDir = dirname(__DIR__) . '/Support/Data/Rbac';
        $empty = <<<EMPTY
<?php

declare(strict_types=1);

return [];
EMPTY;

        \Safe\file_put_contents($rbacDir . '/assignments.php', $empty);
        \Safe\file_put_contents($rbacDir . '/items.php', $empty);

        array_map('unlink', glob($rbacDir . '/rules/*.*'));
    }
}