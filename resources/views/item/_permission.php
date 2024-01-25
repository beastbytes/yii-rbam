<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var array $users
 */

use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Translator\Translator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView;

echo ListView::widget()
    ->dataReader(new IterableDataReader($users))
    ->webView($this)
    ->containerAttributes(['class' => 'list_view'])
    ->header($translator->translate('title.assigned_users'))
    ->emptyText($translator->translate('message.no_users_assigned'))
    ->itemView(static function ($user) use ($urlGenerator) {
         return Html::a(
             content: $user->getName(),
             url: $urlGenerator->generate(
                 'rbam.viewUser',
                 ['id' => $user->getId()]
             )
         )
         ->render();
    })
;
