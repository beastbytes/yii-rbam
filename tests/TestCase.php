<?php

namespace Tests;

use BeastBytes\Yii\Rbam\Support\User\UserRepository;
use PHPUnit\Framework\TestCase as BaseTestCase;
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

    private function userRepository(): UserRepository
    {
        if ($this->userRepository === null) {
            $this->userRepository = new UserRepository();
        }
        return $this->userRepository;
    }
}