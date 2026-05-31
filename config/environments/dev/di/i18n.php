<?php

declare(strict_types=1);

use Yiisoft\I18n\Locale;
use Yiisoft\I18n\LocaleProvider;

/** @var array $params */

return [
    LocaleProvider::class => [
        '__construct()' => [
            new Locale(DEFAULT_LOCALE)
        ],
    ],
];