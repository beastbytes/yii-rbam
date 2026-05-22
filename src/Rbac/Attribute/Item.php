<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac\Attribute;

use BeastBytes\Yii\Rbam\Rbac\ItemInterface;

abstract class Item
{
    public function __construct(
        private readonly string|ItemInterface $name,
        private readonly ?string $description = null,
        private readonly null|string|ItemInterface $parent = null,
        private readonly ?string $ruleName = null,
    )
    {
    }

    public function getDescription(): ?string
    {
        return $this->description ?? sprintf(
            '%s%sdescription',
            $this->getName(),
            ($this->name instanceof ItemInterface ? $this->name->getSeparator() : ' ')
        );
    }

    public function getName(): string
    {
        return $this->name instanceof ItemInterface ? $this->name->getItemName() : $this->name;
    }

    public function getParent(): ?string
    {
        return $this->parent instanceof ItemInterface ? $this->parent->getItemName() : $this->parent;
    }

    public function getRuleName(): ?string
    {
        return $this->ruleName;
    }
}