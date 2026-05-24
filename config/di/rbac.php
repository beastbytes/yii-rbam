<?php

declare(strict_types=1);

use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;


/** @var array $params */

return [
    ManagerInterface::class => [
        'class' => Manager::class,
        'setDefaultRoleNames()' => [array_map(
            fn(array $defaultRole): string => $defaultRole['name'],
            $params['yiisoft/rbac']['defaultRoles']
        )],
        'setGuestRoleName()' => [$params['yiisoft/rbac']['guestRole']['name']],
    ],
    AccessCheckerInterface::class => ManagerInterface::class,
];