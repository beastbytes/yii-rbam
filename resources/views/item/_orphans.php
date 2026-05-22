<?php

declare(strict_types=1);

/**
 * @var string $childType
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var Item[] $orphans
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

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
    ))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->noResultsText($translator->translate(sprintf('message.no-%ss-found', $childType)))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Item $item) => $translator->translate($item->getName()),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $translator->translate($item->getDescription()),
        ),
        new ActionColumn(
            template: '{add}',
            urlCreator: static fn(string $action, DataContext $context) => $urlGenerator->generate(
                'rbam.item.add-child',
                [
                    'child' => $context->data->getName(),
                    'parent' => $parent->getName(),
                    'type' => $type,
                ]
            ),
            buttons: [
                'add' => static fn(string $url, DataContext $context) => Html::button(
                    content: $translator->translate(
                        $type === $childType
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
                            '@click' => sprintf(
                                "\$dispatch('modal', %s)",
                                Json::encode([
                                    'buttons' => [
                                        'continue' => [
                                            'href' => $url,
                                            'data' => [
                                                'child' => substr(
                                                    urldecode($url),
                                                    strrpos(urldecode($url), '/') + 1
                                                ),
                                                'childType' => $childType,
                                                'parent' => $parent->getName(),
                                                'type' => $type,
                                            ]
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate('label.close-dialog'),
                                    'content' => $translator->translate(
                                        sprintf('message.%s.add-child', $childType),
                                        [
                                            'item' => substr(
                                                urldecode($url),
                                                strrpos(urldecode($url), '/') + 1
                                            ),
                                            'parent' => $parent->getName(),
                                        ]
                                    ),
                                    'title' => $translator->translate(
                                        sprintf('header.%s.add-child', $childType),
                                        [
                                            'item' => substr(
                                                urldecode($url),
                                                strrpos(urldecode($url), '/') + 1
                                            ),
                                        ]
                                    ),
                                ])
                            ),
                        ]
                    )
                )
                    ->render()
            ],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true,
            ],
        ),
    )
;