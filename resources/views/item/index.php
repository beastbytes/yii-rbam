<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Inflector $inflector
 * @var array $items
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var Translator $translator
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle(
    $translator->translate('title.' . $type . 's')
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($items))
    ->tableAttributes(['class' => 'grid_view items ' . $type . 's'])
    ->layout('')
    ->layout("{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::a(
                content: $translator->translate(
                    $rbamParameters->getActionButton('add' . ucfirst($type))['content']
                ),
                url: $urlGenerator->generate('rbam.addItem', ['type' => $type]),
                attributes: $rbamParameters->getActionButton('addRole')['attributes'],
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_' . $type . 's' . '_found'))
    ->columns(
        new DataColumn(
            header: ucfirst($type),
            content: static function (Item $item) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $item->getName(),
                    url: ($urlGenerator->generate(
                        'rbam.viewItem',
                        ['name' => $inflector->toSnakeCase($item->getName()), 'type' => $item->getType()]
                    ))
                )
                ->render();
            }
        ),
        new DataColumn(header: 'Description', content: static fn(Item $item) => $item->getDescription()),
        new DataColumn(
            header: 'Created',
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: 'Updated',
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new ActionColumn(
            template: '{view} {update}',
            urlCreator: static function($action, $context) use ($inflector, $urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Item', [
                    'name' => $inflector->toSnakeCase($context->key),
                    'type' => $context->data->getType()
                ]);
            },
            buttons: [
                'update' => new ActionButton(
                    content: $translator->translate($rbamParameters->getActionButton('update')['content']),
                    attributes: $rbamParameters->getActionButton('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getActionButton('view')['content']),
                    attributes: $rbamParameters->getActionButton('view')['attributes'],
                ),
            ]
        )
    )
?>
