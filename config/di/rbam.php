<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\RuleService;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use Yiisoft\Aliases\Aliases;

/** @var array $params */

return [
    RbamParameters::class => [
        'class' => RbamParameters::class,
        '__construct()' => [
            'parameters' => $params['beastbytes/yii-rbam']
        ],
    ],
    RuleServiceInterface::class => static fn (Aliases $aliases) => new RuleService(
        $aliases->get($params['yiisoft/aliases']['aliases']['@rbacRules'])
    ),
];
