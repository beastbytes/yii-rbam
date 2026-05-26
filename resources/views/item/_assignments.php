<?php

declare(strict_types=1);

/**
 * @var Assignment[] $assignments
 * @var string $csrf
 * @var ?int $currentPage
 * @var CurrentUser $currentUser
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\DTO\Assignment;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

$this->registerJs('new Paginator("assignments", ".grid-view nav a")');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view assignments',
        'data-_csrf' => $csrf,
        'data-name' => $item->getName(),
        'id' => 'assignments',
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($assignments)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator(
        $urlGenerator->generate('rbam.item.assignments', ['type' => Item::TYPE_ROLE]))
    )
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{summary}\n{items}\n{pager}")
    ->noResultsText($translator->translate(id: 'message.assignment.none-found', category: 'rbam'))
    ->columns(
        new DataColumn(
            header: $translator->translate(id: 'label.user', category: 'rbam'),
            content: static fn (Assignment $assignment) => $assignment->getUser()->getName(),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
            bodyClass: 'user',
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.assigned-by', category: 'rbam'),
            content: static fn (Assignment $assignment) => $assignment->getRole()->getName(),
            bodyClass: 'assigned-by',
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.assigned-at', category: 'rbam'),
            content: static fn (Assignment $assignment) => (new DateTime())
                ->setTimestamp($assignment->getRole()->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat()),
            bodyClass: 'assigned-at datetime',
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static fn ($action, $context) => $urlGenerator->generate(
                "rbam.user.$action",
                [
                    'id' => $context->data->getUser()->getid()
                ]
            ),
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('view')['content'],
                        category: 'rbam'
                    ),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            visibleButtons: ['view' => $currentUser->can(RbamPermission::userView->getItemName())],
            bodyAttributes: ['class' => 'action'],
        )
    )
    ->render()
;