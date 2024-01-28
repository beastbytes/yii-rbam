<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Inflector $inflector
 * @var Item[] $items
 * @var string $layout
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $toolbar
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

echo GridView::widget()
    ->dataReader(new IterableDataReader($items))
    ->containerAttributes(['class' => 'grid_view items ' . $type . 's'])
    ->header($translator->translate('label.' . $type . 's'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout($layout)
    ->toolbar(
        Html::div(content: $toolbar, attributes: ['class' => 'toolbar'])->render()
    )
    ->emptyText($translator->translate('message.no_' . $type . 's_found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.' . $type),
            content: static fn(Item $item) => $item->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $item->getDescription()
        ),
        new DataColumn(
            header: $translator->translate('label.created_at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: $translator->translate('label.updated_at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static function($action, $context) use ($inflector, $urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Item', [
                    'name' => $inflector->toSnakeCase($context->key),
                    'type' => $context->data->getType()
                ]);
            },
            buttons: [
               'view' => new ActionButton(
                   content: $translator->translate($rbamParameters->getActionButton('view')['content']),
                   attributes: $rbamParameters->getActionButton('view')['attributes'],
               ),
            ]
        )
    )
;

