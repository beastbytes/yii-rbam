<?php

namespace Tests;

use BeastBytes\Yii\Rbam\Support\User\UserRepository;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Support\ActionButtonInterface;
use Tests\Support\TabInterface;
use Tests\Support\RbacTrait;

abstract class TestCase extends BaseTestCase {
    use RbacTrait;

    public const int CURRENT_USER = 24;
    public const int PAGE_SIZE = 20;

    private ?UserRepository $userRepository = null;

    public static function afterAll(): void
    {
        self::clearRbac();
    }

    public static function beforeAll(): void
    {
        self::initRbac();
    }

    //------ Selectors ------//
    protected function actionButton(string $grid, int $row, ActionButtonInterface $actionButton): string
    {
        return trim(sprintf(
            '%s .grid > tbody > tr:nth-child(%d) > td.action > button:nth-child(%d)',
            $grid,
            $row,
            $actionButton->value
        ));
    }

    protected function gridBody(string $grid): string
    {
        return sprintf('%s > .grid > tbody', $grid);
    }

    protected function gridCell(string $grid, int $row, int $column): string
    {
        return sprintf('%s > .grid > tbody > tr:nth-child(%d) > td:nth-child(%d)', $grid, $row, $column);
    }

    protected function tab(TabInterface $tab): string
    {
        return sprintf('.tabs .tab:nth-child(%d)', $tab->value);
    }

    protected function getPageSize(): int
    {
        return self::PAGE_SIZE;
    }

    protected function getUserName(int $id): string
    {
        return self::userRepository()
            ->findById($id)
            ->getName()
        ;
    }

    /* String options are 'first', 'prev' or 'previous', 'next', and 'last' */
    protected function paginatorPage(string $grid, int|string $page): string
    {
        if (is_string($page)) {
            return match ($page) {
                'first' => sprintf('%s > nav:nth-child(4) > a:nth-child(1)', $grid),
                'last' => sprintf('%s > nav:nth-child(4) > a:nth-last-child(1)', $grid),
                'next' => sprintf('%s > nav:nth-child(4) > a:nth-last-child(2)', $grid),
                'prev', 'previous' => sprintf('%s > nav:nth-child(4) > a:nth-child(2)', $grid),
            };
        }

        return sprintf('%s > nav:nth-child(4) > a:nth-child(%d)', $grid, $page + 2);
    }

    private function userRepository(): UserRepository
    {
        if ($this->userRepository === null) {
            $this->userRepository = new UserRepository();
        }
        return $this->userRepository;
    }
}