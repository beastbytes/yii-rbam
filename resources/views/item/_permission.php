<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView;

echo ListView::widget()
    ->dataReader(new IterableDataReader($users))
    ->webView($this)
    ->containerAttributes(['class' => 'list_view'])
    ->header($translator->translate('label.permitted_users') . ' GridView with View action. Role that gives assignment and assigned datetime')
    ->emptyText($translator->translate('message.no_users_permitted'))
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
