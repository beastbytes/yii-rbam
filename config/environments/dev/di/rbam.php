<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Support\User\User;
use BeastBytes\Yii\Rbam\Support\User\UserRepository;
use BeastBytes\Yii\Rbam\User\UserInterface;
use BeastBytes\Yii\Rbam\User\UserRepositoryInterface;

return [
    UserInterface::class => User::class,
    UserRepositoryInterface::class => UserRepository::class,
];