<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use Yiisoft\Auth\IdentityInterface;

interface UserInterface extends IdentityInterface
{
    public function getName(): string;
}