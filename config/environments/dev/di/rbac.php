<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\Php\AssignmentsStorage;
use Yiisoft\Rbac\Php\ItemsStorage;

/** @var array $params */

return [
    AssignmentsStorageInterface::class => static fn (Aliases $aliases) => new AssignmentsStorage(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbac']) . DIRECTORY_SEPARATOR . 'assignments.php'
    ),
    ItemsStorageInterface::class => static fn (Aliases $aliases) => new ItemsStorage(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbac']) . DIRECTORY_SEPARATOR . 'items.php'
    ),
    AccessCheckerInterface::class => [
        'class' => Manager::class,
        'setGuestRoleName()' => [
            'name' => 'guest',
        ],
    ],
];