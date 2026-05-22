<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac;

interface ItemInterface
{
    public function getItemName(): string;
}