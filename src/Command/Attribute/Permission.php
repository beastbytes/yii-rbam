<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command\Attribute;

use Attribute;
use StringBackedEnum;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Permission
{
    public function __construct(
        private StringBackedEnum|string $name,
        private string $description,
        private StringBackedEnum|string $parent,
        private StringBackedEnum|string $ruleName,
    )
    {
    }
}