<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'di' => [
            'yiisoft/form-model' => [
                'config/di.php',
            ],
            'yiisoft/rbac-rules-container' => [
                'config/di.php',
            ],
            'yiisoft/yii-dataview' => [
                'config/di.php',
            ],
            'yiisoft/log-target-file' => [
                'config/di.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/di.php',
            ],
            'yiisoft/router' => [
                'config/di.php',
            ],
            'yiisoft/assets' => [
                'config/di.php',
            ],
            'yiisoft/hydrator' => [
                'config/di.php',
            ],
            'yiisoft/validator' => [
                'config/di.php',
            ],
            'yiisoft/rbac' => [
                'config/di.php',
            ],
            'yiisoft/view' => [
                'config/di.php',
            ],
            'yiisoft/aliases' => [
                'config/di.php',
            ],
            'yiisoft/translator' => [
                'config/di.php',
            ],
            'yiisoft/cache' => [
                'config/di.php',
            ],
            'yiisoft/yii-event' => [
                'config/di.php',
            ],
            '/' => [
                'di/*.php',
            ],
        ],
        'params' => [
            'yiisoft/rbac-rules-container' => [
                'config/params.php',
            ],
            'yiisoft/user' => [
                'config/params.php',
            ],
            'yiisoft/yii-dataview' => [
                'config/params.php',
            ],
            'yiisoft/yii-view-renderer' => [
                'config/params.php',
            ],
            'yiisoft/log-target-file' => [
                'config/params.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/params.php',
            ],
            'yiisoft/router' => [
                'config/params.php',
            ],
            'yiisoft/assets' => [
                'config/params.php',
            ],
            'yiisoft/widget' => [
                'config/params.php',
            ],
            'yiisoft/auth' => [
                'config/params.php',
            ],
            'yiisoft/form' => [
                'config/params.php',
            ],
            'yiisoft/validator' => [
                'config/params.php',
            ],
            'yiisoft/view' => [
                'config/params.php',
            ],
            'yiisoft/csrf' => [
                'config/params.php',
            ],
            'yiisoft/data-response' => [
                'config/params.php',
            ],
            'yiisoft/aliases' => [
                'config/params.php',
            ],
            'yiisoft/translator' => [
                'config/params.php',
            ],
            'yiisoft/session' => [
                'config/params.php',
            ],
            '/' => [
                'params.php',
            ],
        ],
        'di-web' => [
            'yiisoft/user' => [
                'config/di-web.php',
            ],
            'yiisoft/yii-view-renderer' => [
                'config/di-web.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/di-web.php',
            ],
            'yiisoft/view' => [
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
            'yiisoft/yii-event' => [
                'config/di-web.php',
            ],
            '/' => [
                '$di',
            ],
        ],
        'widgets-themes' => [
            'yiisoft/yii-dataview' => [
                'config/widgets-themes.php',
            ],
            '/' => [],
        ],
        'events-web' => [
            'yiisoft/yii-view-renderer' => [
                'config/events-web.php',
            ],
            'yiisoft/log' => [
                'config/events-web.php',
            ],
        ],
        'widgets' => [
            '/' => [],
        ],
        'bootstrap' => [
            'yiisoft/widget' => [
                'config/bootstrap.php',
            ],
            'yiisoft/form' => [
                'config/bootstrap.php',
            ],
            '/' => [],
        ],
        'di-console' => [
            'yiisoft/yii-console' => [
                'config/di-console.php',
            ],
            'yiisoft/yii-event' => [
                'config/di-console.php',
            ],
            '/' => [
                '$di',
                'console/di/*.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-console' => [
                'config/events-console.php',
            ],
            'yiisoft/log' => [
                'config/events-console.php',
            ],
        ],
        'params-console' => [
            'yiisoft/yii-console' => [
                'config/params-console.php',
            ],
            'yiisoft/yii-event' => [
                'config/params-console.php',
            ],
            '/' => [
                '$params',
                'console/params.php',
            ],
        ],
        'params-web' => [
            'yiisoft/yii-event' => [
                'config/params-web.php',
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
        'di-console' => [
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
        'params-console' => [
            '/' => [
                '$params',
                'environments/dev/params.php',
            ],
        ],
    ],
    'test' => [
        'di' => [
            '/' => [],
        ],
        'di-web' => [
            '/' => [
                '$di',
                'environments/test/di/*.php',
            ],
        ],
        'di-console' => [
            '/' => [
                '$di',
                'environments/test/di/*.php',
            ],
        ],
        'params' => [
            '/' => [],
        ],
        'params-web' => [
            '/' => [
                '$params',
                'environments/test/params.php',
            ],
        ],
        'params-console' => [
            '/' => [
                '$params',
                'environments/test/params.php',
            ],
        ],
    ],
];
