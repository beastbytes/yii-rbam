<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var string $childType
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var RbamItem[] $orphans
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Alpine\Modal\Modal;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('paginators.push(new Paginator("orphans", ".grid-view nav a"));');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view orphans',
        'data-_csrf' => $csrf,
        'data-child_type' => $childType,
        'data-parent' => $parent->getName(),
        'data-type' => $type,
        'id' => 'orphans',
    ])
    ->dataReader((new OffsetPaginator(new IterableDataReader($orphans)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate(
        'rbam.item.orphans',
        [
            'childType' => $childType,
            'name' => $parent->getName(),
            'type' => $type,
        ]
    )))
    ->header($translator->translate(
        $childType === Item::TYPE_PERMISSION ? 'label.permissions' : 'label.roles',
        ['name' => $parent->getName()],
        'rbam'
    ))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->noResultsText($translator->translate(
        id: sprintf('message.%s.none-found', $childType),
        category: 'rbam'
    ))
    ->columns(
        new DataColumn(
            header: $translator->translate(id: 'label.name'),
            content: static fn (RbamItem $item) => $item->getItem()->getName(),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
            bodyClass: 'name',
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.description'),
            content: static fn (RbamItem $item) => $translator->translate(
                id: $item->getItem()->getDescription(),
                category: 'rbac-item'
            ),
            bodyClass: 'description',
        ),
        new ActionColumn(
            template: '{add}',
            urlCreator: static fn (string $action, DataContext $context) => $urlGenerator->generate(
                'rbam.item.add-child',
                [
                    'child' => $context->key,
                    'parent' => $parent->getName(),
                    'type' => $type,
                ]
            ),
            buttons: [
                'add' => static fn (string $url, DataContext $context) => (new Modal($assetManager))
                    ->button(
                        Html::button(
                            content: $translator->translate(id: 'button.continue', category: 'rbam'),
                            attributes: [
                                'class' => 'btn btn_continue',
                                '@click' => sprintf(
                                    "rbam.action({href: '%s', data: %s})",
                                    $url,
                                    Json::encode([
                                        'child' => $context->key,
                                        'childType' => $childType,
                                        'parent' => $parent->getName(),
                                        'type' => $type,
                                    ])
                                ),
                            ]
                        ),
                        Html::button(
                            content: $translator->translate(id: 'button.cancel', category: 'rbam'),
                            attributes: [
                                'class' => 'btn btn_cancel',
                            ]
                        ),
                    )
                    ->closeText($translator->translate(id: 'label.close-dialog', category: 'rbam'),)
                    ->content($translator->translate(
                        sprintf('message.%s.add-child-%s', $type, $childType),
                        [
                            'item' => $context->key,
                            'parent' => $parent->getName(),
                        ],
                        'rbam'
                    ))
                    ->title($translator->translate(
                        sprintf('header.%s.add-child-%s', $type, $childType),
                        [
                            'item' => $context->key,
                        ],
                        'rbam'
                    ))
                    ->trigger(Html::button(
                        content: $translator->translate(
                            id: $type === $childType
                                ? $rbamParameters->getButtons('add')['content']
                                : $rbamParameters->getButtons('grant')['content']
                        ),
                        attributes: array_merge(
                            $type === $childType
                                ? $rbamParameters->getButtons('add')['attributes']
                                : $rbamParameters->getButtons('grant')['attributes']
                            ,
                            [
                                'type' => 'button',
                            ]
                        )
                    ))
                    ->render()
            ],
            visibleButtons: [
                'add' => static fn (RbamItem $data) => !$data->isDefaultRole() && !$data->isGuestRole()
            ],
            bodyAttributes: [
                'class' => 'action',
            ],
        ),
    )
;