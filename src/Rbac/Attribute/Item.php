<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac\Attribute;

use BeastBytes\Yii\Rbam\Rbac\ItemInterface;

abstract class Item
{
    /**
     * @param ItemInterface $item
     * @param ?string $description
     * @param ItemInterface|list<ItemInterface> $parent
     * @param string|null $ruleName
     */
    public function __construct(
        private readonly ItemInterface $item,
        private readonly ?string $description = null,
        private readonly array|ItemInterface $parent = [],
        private readonly ?string $ruleName = null,
    )
    {
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function getName(): string
    {
        return $this->item->getItemName();
    }

    public function getParents(): array
    {
       return $this->parent instanceof ItemInterface ? [$this->parent] : $this->parent;
    }

    public function getRuleName(): ?string
    {
        return $this->ruleName;
    }
}