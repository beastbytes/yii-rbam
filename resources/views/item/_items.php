<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var array $actionButtons
 * @var AssetManager $assetManager
 * @var ItemsStorageInterface $itemsStorage
 * @var Csrf $csrf
 * @var DataReaderInterface $dataReader
 * @var string $emptyText
 * @var string $header
 * @var Inflector $inflector
 * @var string $layout
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $toolbar
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Assets\RemoveAsset;
use BeastBytes\Yii\Rbam\ItemTypeService;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Widgets\Dialog;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\View\Renderer\Csrf;

$assetManager->register(RemoveAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$dialog = Dialog::widget()
    ->body($translator->translate('message.remove-' . $type))
    ->footer(
        Html::button(
            $translator->translate('button.continue'),
            [
                'class' => 'btn btn_continue',
                'id' => 'remove-continue',
                'type' => 'submit',
                Dialog::CLOSE_DIALOG_ATTRIBUTE => true,
            ]
        )
            ->render()
        . Html::button(
            $translator->translate('button.cancel'),
            [
                'class' => 'btn btn_cancel',
                'type' => 'reset',
                Dialog::CLOSE_DIALOG_ATTRIBUTE => true,
            ]
        )
            ->render()
    )
    ->header($translator->translate('header.remove-' . $type))
;
echo $dialog->render();

echo GridView::widget()
    ->dataReader($dataReader)
    ->containerAttributes(['class' => 'grid-view ' . $type . 's'])
    ->header($header)
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{summary}\n{items}\n{pager}")
    ->toolbar($toolbar)
    ->emptyText($emptyText)
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
            header: $translator->translate('label.granted-by'),
            content: static function(Item $item) use ($itemsStorage)
            {
                $ancestors = $itemsStorage
                    ->getParents($item->getName())
                ;

                $parent = array_shift($ancestors);

                return $parent === null ? '' : $parent->getName();
            },
            visible: $type === Item::TYPE_PERMISSION
        ),
        new DataColumn(
            header: $translator->translate('label.created-at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: $translator->translate('label.updated-at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new ActionColumn(
            template: '{' . implode('}{', $actionButtons) . '}',
            urlCreator: static function($action, $context) use ($inflector, $urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Item', [
                    'name' => $inflector->toSnakeCase($context->key),
                    'type' => ItemTypeService::getItemType($context->data)
                ]);
            },
            buttons: [
                'remove' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('remove')['content']),
                    attributes: static function() use ($csrf, $dialog, $rbamParameters)
                    {
                        $attributes = $rbamParameters->getButtons('remove')['attributes'];
                        $attributes[Dialog::OPEN_DIALOG_ATTRIBUTE] = $dialog->getId();
                        $attributes['class'] .= ' remove';
                        $attributes['data-csrf'] = $csrf;
                        return $attributes;
                    }
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: ['class' => 'action'],
        )
    )
;