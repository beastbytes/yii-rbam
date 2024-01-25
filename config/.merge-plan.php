<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'params' => [
            'yiisoft/assets' => [
                'config/params.php',
            ],
            'yiisoft/auth' => [
                'config/params.php',
            ],
            'yiisoft/yii-dataview' => [
                'config/params.php',
            ],
            'yiisoft/yii-view' => [
                'config/params.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/params.php',
            ],
            'yiisoft/form' => [
                'config/params.php',
            ],
            'yiisoft/rbac-rules-container' => [
                'config/params.php',
            ],
            'yiisoft/csrf' => [
                'config/params.php',
            ],
            'yiisoft/data-response' => [
                'config/params.php',
            ],
            'yiisoft/log-target-file' => [
                'config/params.php',
            ],
            'yiisoft/aliases' => [
                'config/params.php',
            ],
            'yiisoft/widget' => [
                'config/params.php',
            ],
            'yiisoft/validator' => [
                'config/params.php',
            ],
            'yiisoft/session' => [
                'config/params.php',
            ],
            'yiisoft/view' => [
                'config/params.php',
            ],
            'yiisoft/translator' => [
                'config/params.php',
            ],
            '/' => [
                'params.php',
            ],
        ],
        'di-web' => [
            'yiisoft/assets' => [
                'config/di-web.php',
            ],
            'yiisoft/yii-view' => [
                'config/di-web.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/di-web.php',
            ],
            'yiisoft/csrf' => [
                'config/di-web.php',
            ],
            'yiisoft/data-response' => [
                'config/di-web.php',
            ],
            'yiisoft/error-handler' => [
                'config/di-web.php',
            ],
            'yiisoft/session' => [
                'config/di-web.php',
            ],
            'yiisoft/view' => [
                'config/di-web.php',
            ],
            'yiisoft/yii-event' => [
                'config/di-web.php',
            ],
            '/' => [
                '$di',
            ],
        ],
        'di' => [
            'yiisoft/rbac-rules-container' => [
                'config/di.php',
            ],
            'yiisoft/form-model' => [
                'config/di.php',
            ],
            'yiisoft/yii-dataview' => [
                'config/di.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/di.php',
            ],
            'yiisoft/router' => [
                'config/di.php',
            ],
            'yiisoft/log-target-file' => [
                'config/di.php',
            ],
            'yiisoft/aliases' => [
                'config/di.php',
            ],
            'yiisoft/hydrator' => [
                'config/di.php',
            ],
            'yiisoft/validator' => [
                'config/di.php',
            ],
            'yiisoft/view' => [
                'config/di.php',
            ],
            'yiisoft/cache' => [
                'config/di.php',
            ],
            'yiisoft/yii-event' => [
                'config/di.php',
            ],
            'yiisoft/translator' => [
                'config/di.php',
            ],
            '/' => [
                'di/*.php',
            ],
        ],
        'widgets-themes' => [
            'yiisoft/yii-dataview' => [
                'config/widgets-themes.php',
            ],
            '/' => [],
        ],
        'bootstrap' => [
            'yiisoft/form' => [
                'config/bootstrap.php',
            ],
            'yiisoft/widget' => [
                'config/bootstrap.php',
            ],
            '/' => [],
        ],
        'widgets' => [
            '/' => [],
        ],
        'events-console' => [
            'yiisoft/log' => [
                'config/events-console.php',
            ],
        ],
        'events-web' => [
            'yiisoft/log' => [
                'config/events-web.php',
            ],
        ],
        'params-web' => [
            'yiisoft/yii-event' => [
                'config/params-web.php',
            ],
        ],
        'params-console' => [
            'yiisoft/yii-event' => [
                'config/params-console.php',
            ],
        ],
        'di-console' => [
            'yiisoft/yii-event' => [
                'config/di-console.php',
            ],
        ],
        'bootstrap-web' => [
            '/' => [
                '$bootstrap',
            ],
        ],
        'routes' => [
            '/' => [
                'routes.php',
            ],
        ],
    ],
    'dev' => [
        'di' => [
            '/' => [],
        ],
        'di-web' => [
            '/' => [
                '$di',
                'environments/dev/di/*.php',
            ],
        ],
        'params' => [
            '/' => [],
        ],
        'params-web' => [
            '/' => [
                '$params',
                'environments/dev/params.php',
            ],
        ],
    ],
];
