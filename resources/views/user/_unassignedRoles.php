<?php

declare(strict_types=1);

/**
 * @var Assignment[] $assignments
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var ?int $currentPage
 * @var Permission[] $permissionsGranted
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var Role[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\User\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('paginators.push(new Paginator("unassigned-roles", ".grid-view nav a"));');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view roles',
        'data-_csrf' => $csrf,
        'data-user' => $user->getId(),
        'id' => 'unassigned-roles',
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($unassignedRoles)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate('rbam.user.roles', ['status' => 'unassigned'])))
    ->noResultsText($translator->translate('message.role.none-unassigned'))
    ->header($translator->translate('label.role.unassigned'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Role $role) => $translator->translate($role->getName()),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Role $role) => $translator->translate($role->getDescription()),
        ),
        new ActionColumn(
            content: static fn($data) => Html::button(
                content:$translator->translate($rbamParameters->getButtons('assign')['content']),
                attributes: array_merge(
                    $rbamParameters->getButtons('assign')['attributes'],
                    [
                        'type' => 'button',
                        '@click.prevent' => sprintf(
                            "\$dispatch('modal', %s)",
                            Json::encode([
                                'buttons' => [
                                    'continue' => [
                                        'href' => $urlGenerator->generate('rbam.user.assign-role'),
                                        'data' => [
                                            'item' => $data->getName(),
                                        ],
                                    ],
                                ],
                                'closeDialog' => $translator->translate('label.close-dialog'),
                                'content' => $translator->translate(
                                    'message.user.assign-role',
                                    [
                                        'item' => $data->getName(),
                                        'user' => $user->getName(),
                                    ]
                                ),
                                'title' => $translator->translate(
                                    'header.user.assign-role',
                                    [
                                        'item' => $data->getName(),
                                    ]
                                ),
                            ])
                        )
                    ]
                )
            )
                ->render()
            ,
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ],
            visible: $currentUser->can(RbamPermission::userUpdate->getItemName())
        ),
    )
;