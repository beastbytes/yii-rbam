<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

interface UserRepositoryInterface
{
    /** @return UserInterface[] */
    public function findAll(): array;
    /** @return UserInterface[] */
    public function findByIds(array $ids): array;
}