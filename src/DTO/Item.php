<?php

namespace BeastBytes\Yii\Rbam\DTO;

use \Yiisoft\Rbac\Role;

final class Item
{
    /**
     * @param \Yiisoft\Rbac\Item $item
     * @param list<Role> $parents
     */
    public function __construct(private readonly \Yiisoft\Rbac\Item $item, private readonly array $parents = [])
    {
    }

    public function getItem(): \Yiisoft\Rbac\Item
    {
        return $this->item;
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function isPermission(): bool
    {
        return $this->item->getType() === \Yiisoft\Rbac\Item::TYPE_PERMISSION;
    }
}