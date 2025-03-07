<?php

namespace BeastBytes\Yii\Rbam\Tests\Acceptance;

use BeastBytes\Yii\Rbam\Tests\Support\AcceptanceTester;
use BeastBytes\Yii\Rbam\Tests\Support\Data\User\UserRepository;
use BeastBytes\Yii\Rbam\Tests\Support\Page\Acceptance\Permissions;
use BeastBytes\Yii\Rbam\Tests\Support\Page\Acceptance\Roles;
use BeastBytes\Yii\Rbam\Tests\Support\Page\Acceptance\Rules;
use BeastBytes\Yii\Rbam\Tests\Support\Page\Acceptance\User;
use BeastBytes\Yii\Rbam\Tests\Support\Page\Acceptance\Users;
use Codeception\Attribute\Before;

class InitialStateCest
{
    private const NUMBER_OF_USERS = 51;
    private const PAGE_SIZE = 20;

    #[Before('cleanup')]
    public function rbamIndex(AcceptanceTester $I): void
    {
        $I->amGoingTo('Test the home page on initialisation');
        $I->expectTo('See 4 cards; one each for Roles, Permissions, Rules, and Users');
        $I->expect('Each card to describe what it is showing, have a count of and a button to manage the item');

        $I->amOnPage('/rbam');
        $I->see('Role Based Access Manager', 'html/body/header/h1');

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
        $I->see((string) self::NUMBER_OF_USERS, '.card.users .badge');
        $I->see('Manage Users', '.card.users .btn_manage');
    }

    #[Before('cleanup')]
    public function initialPermissions(AcceptanceTester $I, Permissions $page): void
    {
        $I->amGoingTo('Test Permissions on initialisation');
        $I->expectTo('See that there are no Permissions and a button to create a new Permission');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.permissions .btn']);

        $I->seeInCurrentUrl($page::$URL);
        $I->see('Role Based Access Manager', 'html/body/header/h1');

        $page->gridViewHeader();
        $page->gridHeader();
        $page->emptyGrid();

        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    #[Before('cleanup')]
    public function initialRoles(AcceptanceTester $I, Roles $page): void
    {
        $I->amGoingTo('Test Roles on initialisation');
        $I->expectTo('See that there are no Roles and a button to create a new Role');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.roles .btn']);

        $I->seeInCurrentUrl($page::$URL);
        $I->see('Role Based Access Manager', 'html/body/header/h1');

        $page->gridViewHeader();
        $page->gridHeader();
        $page->emptyGrid();

        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    #[Before('cleanup')]
    public function initialRules(AcceptanceTester $I, Rules $page): void
    {
        $I->amGoingTo('Test Rules on initialisation');
        $I->expectTo('See that there are no Rules and a button to create a new Rule');

        $I->amOnPage('/rbam');
        $I->click(['css' => '.rules .btn']);

        $I->seeInCurrentUrl($page::$URL);
        $I->see('Role Based Access Manager', 'html/body/header/h1');

        $page->gridViewHeader();
        $page->gridHeader();
        $page->emptyGrid();

        $I->seeElement('.grid-view .toolbar .btn_create');
    }

    #[Before('cleanup')]
    public function initialUsers(AcceptanceTester $I, Users $page): void
    {
        $I->amGoingTo('Test Users on initialisation');
        $I->expectTo('See that there are Users and that none have been assigned any roles and therefore not granted any permissions');

        $I->amOnPage('/rbam');
        $I->click(['xpath' => 'html/body/div[1]/div/div[4]/div[2]/a']);

        $I->seeInCurrentUrl($page::$URL);
        $I->see('Role Based Access Manager', 'html/body/header/h1');

        $page->gridViewHeader();
        $page->gridHeader();

        $pages = (int)ceil(self::NUMBER_OF_USERS / self::PAGE_SIZE);
        for ($pageNumber = 1; $pageNumber <= $pages; $pageNumber++) {
            $usersOnPage = $pageNumber < $pages ? self::PAGE_SIZE : self::NUMBER_OF_USERS % self::PAGE_SIZE;

            if ($pageNumber > 1) {
                $I->amOnPage($page::$URL . "?page=$pageNumber");
            }

            for ($i = 1; $i <= $usersOnPage; $i++) {
                $I->seeUserHasName(
                    $I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[1]"),
                );
                $I->seeUserHasPermissions(
                    ' ',
                    $I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[2]"),
                );
                $I->seeUserHasRoles(
                    ' ',
                    $I->grabTextFrom("html/body/div[1]/div/table/tbody/tr[$i]/td[2]"),
                );
                $userId = self::PAGE_SIZE * ($pageNumber - 1) + $i;
                $I->seeLink('View', "/rbam/user/$userId");
            }
        }
    }

    #[Before('cleanup')]
    public function initialUser(AcceptanceTester $I, User $page, UserRepository $userRepository): void
    {
        $I->amGoingTo('Check each user');
        for ($i = 1; $i <= self::NUMBER_OF_USERS; $i++) {
            $I->amOnPage("/rbam/user/$i");

            $user = $userRepository->findById((string) $i);

            $I->dontSee('Revoke All');

            $page->name($user->getName());
            $page->permissionsGridViewHeader();
            $page->permissionsGridHeader();
            $page->permissionsEmptyGrid();
            $page->rolesGridViewHeader();
            $page->rolesGridHeader();
            $page->rolesEmptyGrid();
        }
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