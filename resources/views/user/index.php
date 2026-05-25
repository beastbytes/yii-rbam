<?php

declare(strict_types=1);

/**
 * @var string $csrf
 * @var int $currentPage
 * @var CurrentUser $currentUser
 * @var Inflector $inflector
 * @var ManagerInterface $rbacManager
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var RbamUser[] $users
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\DTO\User as RbamUser;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

$this->registerJs(sprintf('rbam = new Rbam("users")'));
$this->registerJs(sprintf('paginators.push(new Paginator("users", ".grid-view nav a"));'));

$this->setTitle($translator->translate('label.users'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

$this->setParameter(
    'baseUrl',
    $urlGenerator->generate('rbam.user.index')
);
?>

<?= GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view users',
        'data-_csrf' => $csrf,
        'id' => 'users',
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($users)))
        ->withCurrentPage($currentPage)
        ->withPageSize($rbamParameters->getPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate('rbam.user.index')))
    ->header($this->getTitle())
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->noResultsText($translator->translate('message.user.none-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(RbamUser $user): string => $user->getUser()->getName(),
            bodyClass: 'name',
        ),
        new DataColumn(
            header: $translator->translate('label.roles'),
            content: static fn(RbamUser $user): int => $user->getRoleCount(),
            bodyClass: 'number roles',
        ),
        new DataColumn(
            header: $translator->translate('label.permissions'),
            content: static fn(RbamUser $user): int => $user->getPermissionCount(),
            bodyClass: 'number permissions',
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static fn($action, $context) => $urlGenerator->generate(
                'rbam.user.view', [
                    'id' => $context->data->getUser()->getId()
                ]
            ),
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            visibleButtons: ['view' => $currentUser->can(RbamPermission::userView->getItemName())],
            bodyAttributes: ['class' => 'action']
        )
    )
?>