<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;

/** @var array $params */

return [
    ManagerInterface::class => [
        'class' => Manager::class,
        'setGuestRoleName()' => [
            'name' => $params['yiisoft/rbac']['guestRole']
        ],
    ],
];
