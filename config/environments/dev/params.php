<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Support\ViewInjection\CommonViewInjection;
use BeastBytes\Yii\Rbam\Support\ViewInjection\LayoutViewInjection;
use Yiisoft\Csrf\CsrfTokenMiddleware;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Yii\View\Renderer\CsrfViewInjection;

const LOCALE = 'en-gb';

return [
    'app' => [
        'charset' => 'UTF-8',
        'locale' => LOCALE,
        'name' => 'Yii RBAM',
    ],
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        CsrfTokenMiddleware::class,
        Router::class,
    ],
    'traceLink' => 'phpstorm://open?url=file://{file}&line={line}',
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
        'locale' => LOCALE,
        'fallbackLocale' => 'en',
        'defaultCategory' => 'rbam',
    ],
    'yiisoft/view' => [
        'basePath' => '@views'
    ],
    'yiisoft/yii-view-renderer' => [
        'viewPath' => '@views',
        'layout' => '@layout/main',
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
        ],
    ],
];