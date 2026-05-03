<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

interface UserRepositoryInterface
{
    /** @return UserInterface[] */
    public function findAll(): array;
    /** @return UserInterface[] */
    public function findByIds(array $ids): array;
}