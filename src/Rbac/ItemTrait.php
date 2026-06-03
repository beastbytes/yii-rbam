<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix;
use ReflectionAttribute;
use ReflectionClass;

trait ItemTrait
{
    public function getItemDescription(): string
    {
        return $this->getItemName() . $this->getSeparator() . self::DESCRIPTION;
    }

    public function getItemName(): string
    {
        return $this->prefixAttribute()?->getPrefix() . $this->value;
    }

    private function getSeparator(): ?string
    {
        return $this->prefixAttribute()?->getSeparator();
    }

    private function prefixAttribute(): ?Prefix
    {
        $attributes = (new ReflectionClass($this))
            ->getAttributes(Prefix::class, ReflectionAttribute::IS_INSTANCEOF)
        ;

        if (count($attributes) > 0) {
            return $attributes[0]->newInstance();
        }

        return null;
    }
}