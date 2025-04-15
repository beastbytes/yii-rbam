<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var array $actionButtons
 * @var AssetManager $assetManager
 * @var ?int $currentPage
 * @var ItemsStorageInterface $itemsStorage
 * @var Csrf $csrf
 * @var string $emptyText
 * @var string $header
 * @var Inflector $inflector
 * @var Item $item
 * @var Item[] $items
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $toolbar
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 * @var ?UserInterface $user
 */

use BeastBytes\Yii\Rbam\Assets\RemoveAsset;
use BeastBytes\Yii\Rbam\ItemTypeService;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Widgets\Dialog;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
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
$this->addJsStrings(['pagination.init("' . $type . '")']);

if ($item instanceof Item) {
    $this->setParameter(
        'baseUrl',
        $urlGenerator->generate('rbam.itemPagination')
    );
} else {
    $this->setParameter(
        'baseUrl',
        $urlGenerator->generate('rbam.permissionsPagination')
    );
}

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

$containerAttributes = [
    'class' => 'grid-view ' . $type . 's',
    'id' => $type,
    'data-_csrf' => $csrf,
    'data-action-buttons' => implode(',', $actionButtons),
    'data-header' => $header,
    'data-toolbar' => $toolbar,
];

if ($item instanceof Item) {
    $containerAttributes['data-name'] = $item->getName();
    $containerAttributes['data-type'] = $type;
} elseif ($user instanceof UserInterface) {
    $containerAttributes['data-userId'] = $user->getId();
}

echo GridView::widget()
    ->dataReader((new OffsetPaginator(new IterableDataReader($items)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->containerAttributes($containerAttributes)
    ->header($translator->translate($header))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{summary}\n{items}\n{pager}")
    ->toolbar($toolbar)
    ->urlCreator(function (array $arguments, array $queryParameters): string {
        $baseUrl = $this->getParameter('baseUrl');
        $pathParams = [];

        // Handle path parameters
        foreach ($arguments as $name => $value) {
            $pathParams[] = "$name-$value";
        }

        // Build final URL
        $url = $baseUrl;
        if ($pathParams) {
            $url .= '/' . implode('/', $pathParams);
        }
        if ($queryParameters) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    })
    ->emptyText($translator->translate($emptyText))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
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