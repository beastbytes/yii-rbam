<?php

declare(strict_types=1);

/**
 * @var RbamItem[] $children
 * @var string $childType
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\RbamParameters;
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

$this->registerJs('paginators.push(new Paginator("children", ".grid-view nav a"));');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view children',
        'data-_csrf' => $csrf,
        'data-child_type' => $childType,
        'data-parent' => $parent->getName(),
        'data-type' => $type,
        'id' => 'children'
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($children)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate(
        'rbam.item.children',
        [
            'childType' => $childType,
            'name' => $parent->getName(),
            'type' => $type,
        ]
    )))
    ->header($translator->translate(
        $type === Item::TYPE_PERMISSION
            ? 'label.child-permissions'
            : ($childType === Item::TYPE_PERMISSION ? 'label.permissions.granted' : 'label.child-roles')
    ))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{toolbar}\n{summary}\n{items}\n{pager}")
    ->toolbar(!empty($children)
        ? Html::div(
            content: Html::button(
                content: $translator->translate($rbamParameters->getButtons('removeAll')['content']),
                attributes: array_merge(
                    $rbamParameters->getButtons('removeAll')['attributes'],
                    [
                        'type' => 'button',
                        '@click.prevent' => sprintf(
                            "\$dispatch('modal', %s)",
                            Json::encode([
                                'buttons' => [
                                    'continue' => [
                                        'href' => $urlGenerator->generate(
                                            'rbam.item.remove-child',
                                            [
                                                'parent' => $parent->getName(),
                                                'type' => $type,
                                            ]
                                        ),
                                        'data' => [
                                            'childType' => $childType,
                                            'parent' => $parent->getName(),
                                            'type' => $type,
                                        ],
                                    ],
                                ],
                                'closeDialog' => $translator->translate('label.close-dialog'),
                                'content' => $translator->translate(
                                    sprintf('message.%s.remove-all', $childType),
                                    [
                                        'parent' => $parent->getName(),
                                    ]
                                ),
                                'title' => $translator->translate(sprintf('header.%s.remove-all', $childType)),
                            ])
                        ),
                    ]
                )
            ),
            attributes: [
                'class' => 'toolbar',
                'x-data' => true,
            ]
        )
            ->render()
        : ''
    )
    ->noResultsText($translator->translate(sprintf('message.%s.none-found', $childType)))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(RbamItem $item) => $translator->translate($item->getItem()->getName()),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
            bodyClass: 'name',
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(RbamItem $item) => $translator->translate($item->getItem()->getDescription()),
            bodyClass: 'description',
        ),
        new ActionColumn(
            template: '{remove}',
            urlCreator: static fn(string $action, DataContext $context) => $urlGenerator->generate(
                'rbam.item.remove-child',
                [
                    'child' => $context->data->getItem()->getName(),
                    'parent' => $parent->getName(),
                    'type' => $type,
                ]
            ),
            buttons: [
                'remove' => static fn(string $url, DataContext $context) => Html::button(
                    content: $translator->translate($rbamParameters->getButtons('remove')['content']),
                    attributes: array_merge(
                        $rbamParameters->getButtons('remove')['attributes'],
                        [
                            'type' => 'button',
                            '@click' => sprintf(
                                "\$dispatch('modal', %s)",
                                Json::encode([
                                    'buttons' => [
                                        'continue' => [
                                            'href' => $url,
                                            'data' => [
                                                'child' => $context->key,
                                                'childType' => $childType,
                                                'parent' => $parent->getName(),
                                                'type' => $type,
                                            ]
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate('label.close-dialog'),
                                    'content' => $translator->translate(
                                        sprintf('message.%s.remove-child', $childType),
                                        [
                                            'child' => $context->key,
                                            'parent' => $parent->getName(),
                                        ]
                                    ),
                                    'title' => $translator->translate(
                                        sprintf('header.%s.remove-child', $childType),
                                        [
                                            'child' => $context->key,
                                        ]
                                    ),
                                ])
                            ),
                        ]
                    )
                )
                    ->render()
            ],
            visibleButtons: ['remove' => fn(RbamItem $item) => $item->isChild()],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true,
            ],
        ),
    )
;