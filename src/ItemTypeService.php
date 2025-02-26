<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use Yiisoft\Rbac\Item;

/**
 * Manages difference between [RBAC](https://github.com/yiisoft/rbac) V2 and later versions
 *
 * RBAC V2 returns a string for item type, V3 and later returns an int
 */
final class ItemTypeService
{
    public static function getItemType(Item $item): string
    {
        /** @var int|string $type */
        $type = $item->getType();

        return match($type) {
            1 => 'role',
            2 => 'permission',
            default => (string) $type
        };
    }
}