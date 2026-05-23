<?php

declare(strict_types=1);

/**
 * @var array $actionButtons
 * @var AssetManager $assetManager
 * @var ?int $currentPage
 * @var CurrentUser $currentUser
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamItem[] $items
 * @var string $header
 * @var ?string $noResultsText
 * @var string $paginationUrl
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var NormalTag $toolbar
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 * @var ?UserInterface $user
 */

use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\User\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Html\Tag\Base\NormalTag;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs(sprintf('paginators.push(new Paginator("%s", ".grid-view nav a"));', $type));

$containerAttributes = [
    'class' => sprintf('grid-view %s', $type),
    'data-_csrf' => $csrf,
    'data-action_buttons' => implode(',', $actionButtons),
    'data-no_results_text' => $noResultsText,
    'data-pagination_url' => $paginationUrl,
    'data-toolbar' => $toolbar,
    'id' => $type,
];

if (isset($user)) {
    $containerAttributes['data-user'] = $user->getId();
} else {
    if ($item instanceof Item) {
        $containerAttributes['data-name'] = $item->getName();
    }
    $containerAttributes['data-header'] = $header;
    $containerAttributes['data-type'] = $type;
}

echo GridView::widget()
    ->containerAttributes($containerAttributes)
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($items)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($paginationUrl))
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->header(!empty($header) ? $translator->translate($header) : '')
    ->headerClass('header')
    ->toolbar(Html::div(NoEncode::string((string) $toolbar), ['class' => 'toolbar'])->render())
    ->layout((!empty($header) ? "{header}\n" : '') . "{toolbar}\n{summary}\n{items}\n{pager}")
    ->noResultsText($noResultsText ? $translator->translate($noResultsText, ['type' => $type]) : $noResultsText)
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(RbamItem $item) => $translator->translate($item->getItem()->getName()),
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(RbamItem $item) => $translator->translate($item->getItem()->getDescription()),
        ),
        new DataColumn(
            header: $translator->translate('label.rule'),
            content: static fn(RbamItem $item) => is_string($item->getItem()->getRuleName())
                ? substr($item->getItem()->getRuleName(), 30, -4)
                : $translator->translate('message.no-rule')
            ,
        ),
        new DataColumn(
            header: $translator->translate('label.granted-by'),
            content: static function (RbamItem $item) use ($translator) {
                $grantedBy = [];

                foreach ($item->getParents() as $parent) {
                    $grantedBy[] = $translator->translate($parent->getName());
                }

                return '<div>' . implode('</div><div>', $grantedBy) . '</div>';
            },
            encodeContent: false,
            visible: $type === Item::TYPE_PERMISSION
        ),
        new DataColumn(
            header: $translator->translate('label.created-at'),
            content: static fn(RbamItem $item) => (new DateTime())
                ->setTimestamp($item->getItem()->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: $translator->translate('label.updated-at'),
            content: static fn(RbamItem $item) => (new DateTime())
                ->setTimestamp($item->getItem()->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new ActionColumn(
            template: '{' . implode('}{', $actionButtons) . '}',
            urlCreator: static fn($action, $context) => $urlGenerator
                ->generate(
                    'rbam.item.' . $action,
                    [
                        'name' => $context->key,
                        'type' => $context->data->getItem()->getType(),
                    ]
                )
            ,
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
                                                'item' => substr(
                                                    urldecode($url),
                                                    strpos(urldecode($url), '/', 7) + 1,
                                                    strrpos(urldecode($url), '/')
                                                        - strpos(urldecode($url), '/', 7) - 1
                                                ),
                                            ]
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate('label.close-dialog'),
                                    'content' => $translator->translate(
                                        sprintf('message.%s.remove', $type),
                                        [
                                            'item' => substr(
                                                urldecode($url),
                                                strpos(urldecode($url), '/', 7) + 1,
                                                strrpos(urldecode($url), '/')
                                                    - strpos(urldecode($url), '/', 7) - 1
                                            ),
                                        ]
                                    ),
                                    'title' => $translator->translate(
                                        sprintf('header.%s.remove', $type),
                                        [
                                            'item' => substr(
                                                urldecode($url),
                                                strpos(urldecode($url), '/', 7) + 1,
                                                strrpos(urldecode($url), '/')
                                                    - strpos(urldecode($url), '/', 7) - 1
                                            ),
                                        ]
                                    ),
                                ])
                            ),
                        ]
                    )
                )
                    ->render()
                ,
                'update' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('update')['content']),
                    attributes: $rbamParameters->getButtons('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            visibleButtons: [
                'remove' => $currentUser->can(RbamPermission::itemRemove->getItemName()),
                'update' => $currentUser->can(RbamPermission::itemUpdate->getItemName()),
                'view' => $currentUser->can(RbamPermission::itemUpdate->getItemName()),
            ],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ],
        )
    )
;