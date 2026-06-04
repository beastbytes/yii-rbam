<?php

declare(strict_types=1);

use Yiisoft\Csrf\CsrfTokenMiddleware;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;

const DEFAULT_LOCALE = 'en-GB'; // must be in locales
const LOCALES = [
    // ISO 3166 alpha-2 => BCP 47 formatted string
    'de' => 'de-DE',
    'fr' => 'fr-FR',
    'gb' => 'en-GB',
];

return [
    'app' => [
        'charset' => 'UTF-8',
        'name' => 'Yii RBAM',
    ],
    'locale' => [
        'defaultLocale' => DEFAULT_LOCALE,
        'locales' => LOCALES,
        'ignoredRequests' => [
            '/gii**',
            '/debug**',
            '/inspect**',
        ],
        'queryParameterName' => 'locale',
    ],
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        CsrfTokenMiddleware::class,
        Router::class,
    ],
    'traceLink' => 'phpstorm://open?url=file://{file}&line={line}',
    'beastbytes/yii-rbam' => [
        'applicationLayout' => '@root/support/views/main',
    ],
    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__, 3),
            '@baseUrl' => '/',
            '@assets' => '@root/public_html/assets',
            '@assetsSource' => '@resources/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@dev' => '@root/dev',
            '@layout' => '@views/layout',
            '@messages' => '@resources/messages',
            '@public' => '@root/public',
            '@rbac' => '@root/rbac',
            '@rbacRules' => '@root/rbac/rules',
            '@rbacTranslations' => '@root/rbac/translations',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root/src',
            '@vendor' => '@root/vendor',
            '@views' => '@resources/views',
        ],
    ],
    'yiisoft/assets' => [
        'assetPublisher' => [
            'forceCopy' => true,
            'linkAssets' => false,
        ],
    ],
    'yiisoft/translator' => [
        'locale' => DEFAULT_LOCALE,
        'fallbackLocale' => DEFAULT_LOCALE,
        'defaultCategory' => 'rbam',
    ],
    'yiisoft/view' => [
        'basePath' => '@views'
    ],
    'yiisoft/rbac' => [
        'defaultRoles' => [[ // list<array{name: string, description: string}>
            'name' => 'default.role',
            'description' => 'default.role.description',
        ]],
        'guestRole' => [ // array{name: string, description: string}
            'name' => 'guest.role',
            'description' => 'guest.role.description',
        ]
    ],
];