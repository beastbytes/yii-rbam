<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Permission
{
    public function __construct(
        private \Enum|string $name,
        private ?string $description = null,
        private \Enum|string|null $parent = null,
        private ?string $ruleName = null,
    )
    {
    }
}