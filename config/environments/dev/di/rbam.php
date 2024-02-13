<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Dev\User\User;
use BeastBytes\Yii\Rbam\Dev\User\UserRepository;
use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;

return [
    UserInterface::class => User::class,
    UserRepositoryInterface::class => UserRepository::class,
];
