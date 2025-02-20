<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/** @var array $params */

use BeastBytes\Yii\Rbam\RulesMiddleware;

return [
    RulesMiddleware::class => [
        'class' => RulesMiddleware::class,
        'rules()' => [
            'rules' => array_keys($params['yiisoft/rbac-rules-container']['rules']),
        ],
    ]
];