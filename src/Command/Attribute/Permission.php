<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command\Attribute;

use Attribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use StringBackedEnum;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Permission
{
    public function __construct(
        private RbamPermission|string $name,
        private string $description,
        private StringBackedEnum|string $parent,
        private string $ruleName,
    )
    {
    }
}