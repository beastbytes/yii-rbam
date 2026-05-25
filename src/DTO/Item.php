<?php

namespace BeastBytes\Yii\Rbam\DTO;

use \Yiisoft\Rbac\Role;

final class Item
{
    private bool $isChild = false;
    private bool $isDefaultRole = false;
    private bool $isGuestRole = false;
    private array $parents = [];

    /**
     * @param \Yiisoft\Rbac\Item $item
     */
    public function __construct(private readonly \Yiisoft\Rbac\Item $item)
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

    public function isChild(): bool
    {
        return $this->isChild;
    }

    public function isDefaultRole(): bool
    {
        return $this->isDefaultRole;
    }

    public function isGuestRole(): bool
    {
        return $this->isGuestRole;
    }

    /**
     * Indicates whether the item is a direct child in the current context
     *
     * Used by child role and permission management to determine if an item can be removed from the ancestor role
     *
     * @param bool $isChild
     */
    public function withIsChild(bool $isChild): self
    {
        $new = clone $this;
        $new->isChild = $isChild;
        return $new;
    }

    public function withIsDefaultRole(bool $isDefaultRole): self
    {
        $new = clone $this;
        $new->isDefaultRole = $isDefaultRole;
        return $new;
    }

    public function withIsdGuestRole(bool $isGuestRole): self
    {
        $new = clone $this;
        $new->isGuestRole = $isGuestRole;
        return $new;
    }

    /**
     * @param list<Role> $parents
     */
    public function withParents(array $parents): self
    {
        $new = clone $this;
        $new->parents = $parents;
        return $new;
    }
}