<?php

declare(strict_types=1);

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;

/** @var array $params */

return [
    ManagerInterface::class => [
        'class' => Manager::class,
        '__construct()' => [
            'includeRolesInAccessChecks' => true,
        ],
    ],
    AccessCheckerInterface::class => ManagerInterface::class,
];