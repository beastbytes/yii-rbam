<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;

/** @var array $params */

return [
    // Configure application CategorySource
    'translation.rbam' => [
        'definition' => static function (Aliases $aliases) {
            return new CategorySource(
                'rbam',
                new MessageSource($aliases->get('@messages')),
                new IntlMessageFormatter(),
            );
        },
        'tags' => ['translation.categorySource'],
    ]
];