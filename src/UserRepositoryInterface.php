<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

interface UserRepositoryInterface
{
    /** @psalm-return UserInterface[] */
    public function findAll(): array;
    public function findAllIds(): array;
    public function findById(string $id): UserInterface;
    /** @psalm-return UserInterface[] */
    public function findByIds(array $ids): array;
}