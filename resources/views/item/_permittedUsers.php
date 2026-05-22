<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var int $currentPage
 * @var Permission $permission
 * @var PermittedUser[] $permittedUsers
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\DTO\PermittedUser;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\Permission;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('new Paginator("permitted-users", ".grid-view nav a")');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view permitted-users',
        'data-_csrf' => $csrf,
        'id' => 'permitted-users',
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($permittedUsers)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate(
        'rbam.item.permitted-users',
        [
            'name' => $permission->getName(),
            'type' => 'permission'
        ]
    )))
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{summary}\n{items}\n{pager}")
    ->noResultsText($translator->translate('message.user.none-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.user'),
            content: static fn(PermittedUser $permittedUser) => $permittedUser->getUser()->getName(),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: $translator->translate('label.granted-by'),
            content: static fn(PermittedUser $permittedUser)
                => $translator->translate($permittedUser->getRole()->getName())
            ,
        ),
        new DataColumn(
            header: $translator->translate('label.assigned-at'),
            content: static fn(PermittedUser $permittedUser) => (new DateTime())
                ->setTimestamp($permittedUser->getAssignment()->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static fn($action, $context) => $urlGenerator->generate(
                "rbam.user.$action",
                [
                    'id' => $context->data->getUser()->getid()
                ]
            ),
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: ['class' => 'action'],
        )
    )
    ->render()
;