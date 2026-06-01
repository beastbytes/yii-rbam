<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;

/** @var array $params */

return [
    'translation.rbac-item' => [
        'definition' => static fn (Aliases $aliases) => new CategorySource(
            'rbac-item',
            new MessageSource($aliases->get('@rbacTranslations')),
            new IntlMessageFormatter(),
            new MessageSource($aliases->get('@rbacTranslations')),
        ),
        'tags' => ['translation.categorySource'],
    ],
    'translation.rbac-rule' => [
        'definition' => static fn (Aliases $aliases) => new CategorySource(
            'rbac-rule',
            new MessageSource($aliases->get('@rbacTranslations')),
            new IntlMessageFormatter(),
            new MessageSource($aliases->get('@rbacTranslations'))
        ),
        'tags' => ['translation.categorySource'],
    ],
    'translation.rbam' => [
        'definition' => static fn (Aliases $aliases) => new CategorySource(
            'rbam',
            new MessageSource($aliases->get('@messages')),
            new IntlMessageFormatter(),
        ),
        'tags' => ['translation.categorySource'],
    ]
];