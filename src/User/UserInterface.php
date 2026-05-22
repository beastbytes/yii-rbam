<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\User;

use Yiisoft\Auth\IdentityInterface;

interface UserInterface extends IdentityInterface
{
    public function getName(): string;
}