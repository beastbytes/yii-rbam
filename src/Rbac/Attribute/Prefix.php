<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Prefix
{
    /**
     * @param list<string>|non-empty-string $prefix
     * @param non-empty-string $separator
     */
    public function __construct(private readonly array|string $prefix, private readonly string $separator = ' ')
    {
    }

    public function getPrefix(): string
    {
        return (is_array($this->prefix) ? implode($this->separator, $this->prefix) : $this->prefix) . $this->separator;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }
}