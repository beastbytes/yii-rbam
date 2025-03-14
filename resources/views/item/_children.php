<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Item[] $children
 * @var ?int $currentPage
 * @var Item[] $descendants
 * @var Inflector $inflector
 * @var Item[] $items
 * @var ManagerInterface $manager
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

echo GridView::widget()
    ->dataReader((new OffsetPaginator(new IterableDataReader($descendants)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->containerAttributes(['class' => 'grid-view children'])
    ->header($translator->translate(
        $type === Item::TYPE_PERMISSION ? 'label.permissions-granted' : 'label.descendant-roles'
    ))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->toolbar(
        !empty($descendants)
            ? Html::button(
                $translator->translate($rbamParameters->getButtons('removeAll')['content']),
                array_merge(
                    $rbamParameters->getButtons('removeAll')['attributes'],
                    [
                        'data-href' => $urlGenerator->generate('rbam.removeAllChildren'),
                        'id' => 'all_items',
                        'type' => 'button',
                    ],
                ),
            )
                ->render()
            : '',
    )
    ->emptyText($translator->translate(
        $type === Item::TYPE_PERMISSION
            ? 'message.no-permissions-granted'
            : 'message.no-child-roles',
    ))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Item $item) => $item->getName(),
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $item->getDescription(),
        ),
        new ActionColumn(
            content: static function (Item $item) use (
                $children,
                $rbamParameters,
                $translator,
                $urlGenerator
            )
            {
                return Html::button(
                    $translator->translate($rbamParameters->getButtons('remove')['content']),
                    array_merge(
                        $rbamParameters->getButtons('remove')['attributes'],
                        [
                            'data-name' => $item->getName(),
                            'data-href' => $urlGenerator->generate('rbam.removeChild'),
                            'disabled' => !in_array($item->getName(), $children),
                        ]
                    )
                );
            },
            bodyAttributes: ['class' => 'action'],
        ),
    )
;

echo GridView::widget()
    ->dataReader((new OffsetPaginator(new IterableDataReader($items)))
        ->withCurrentPage($currentPage)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->containerAttributes(['class' => 'grid-view children'])
    ->header($translator->translate(
        $type === Item::TYPE_PERMISSION ? 'label.permissions' : 'label.roles',
        ['name' => $parent->getName()],
    ))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->emptyText($translator->translate(
        $type === Item::TYPE_PERMISSION ? 'message.no-permissions-found' : 'message.no-roles-found',
    ))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Item $item) => $item->getName(),
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $item->getDescription(),
        ),
        new ActionColumn(
            content: static function (Item $item) use (
                $manager,
                $parent,
                $rbamParameters,
                $translator,
                $type,
                $urlGenerator
            )
            {
                return Html::button(
                    $translator->translate($type === Item::TYPE_PERMISSION
                        ? $rbamParameters->getButtons('grant')['content']
                        : $rbamParameters->getButtons('add')['content']
                    ),
                    array_merge(
                        $type === Item::TYPE_PERMISSION
                            ? $rbamParameters->getButtons('grant')['attributes']
                            : $rbamParameters->getButtons('add')['attributes']
                        ,
                        [
                            'data-name' => $item->getName(),
                            'data-href' => $urlGenerator->generate('rbam.addChild'),
                            'disabled' => !$manager->canAddChild($parent->getName(), $item->getName()),
                        ]
                    )
                );
            },
            bodyAttributes: ['class' => 'action'],
        ),
    )
;