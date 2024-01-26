<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Php\AssignmentsStorage;
use Yiisoft\Rbac\Php\ItemsStorage;

/** @var array $params */

return [
    ItemsStorageInterface::class => static fn (Aliases $aliases) => new ItemsStorage(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbac'])
    ),
    AssignmentsStorageInterface::class => static fn (Aliases $aliases) => new AssignmentsStorage(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbac'])
    ),
];
