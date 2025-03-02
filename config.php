<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

return [
    'config-plugin' => [
        'bootstrap' => [],
        'bootstrap-web' => [
            '$bootstrap',
        ],
        'di' => 'di/*.php',
        'di-web' => [
            '$di',
        ],
        'params' => 'params.php',
        'routes' => 'routes.php',
        'widgets' => [],
        'widgets-themes' => [],
    ],
    'config-plugin-environments' => [
        'dev' => [
            'di' => [],
            'di-web' => [
                '$di',
                'environments/dev/di/*.php',
            ],
            'params' => [],
            'params-web' => [
                '$params',
                'environments/dev/params.php',
            ],
        ],
        'test' => [
            'di' => [],
            'di-web' => [
                '$di',
                'environments/test/di/*.php',
            ],
            'params' => [],
            'params-web' => [
                '$params',
                'environments/test/params.php',
            ],
        ],
        /*
        'prod' => [
            'params' => [
                'environments/prod/params.php',
            ],
        ],
        */
    ],
    'config-plugin-options' => [
        'source-directory' => 'config',
    ],
];
