<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\User;

interface UserRepositoryInterface
{
    public function count(): int;
    /** @return UserInterface[] */
    public function findAll(): array;
    /** @return UserInterface[] */
    public function findByIds(array $ids): array;
}