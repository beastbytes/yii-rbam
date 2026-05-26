<?php

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var string[] $assignments
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var CurrentUser $currentUser
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\DTO\Item;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\User\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('paginators.push(new Paginator("assigned-roles", ".grid-view nav a"));');

echo GridView::widget()
    ->containerAttributes([
        'class' => 'grid-view roles',
        'data-_csrf' => $csrf,
        'data-user' => $user->getId(),
        'id' => 'assigned-roles',
    ])
    ->containerTag('div')
    ->dataReader((new OffsetPaginator(new IterableDataReader($assignedRoles)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate('rbam.user.roles', ['status' => 'assigned'])))
    ->header($translator->translate(id: 'label.role.assigned', category: 'rbam'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{toolbar}\n{summary}\n{items}\n{pager}")
    ->toolbar($currentUser->can(RbamPermission::userUpdate->getItemName()) && !empty($assignedRoles)
        ? Html::div(
            content: Html::button(
                content: $translator->translate(id: $rbamParameters->getButtons('revokeAll')['content']),
                attributes: array_merge(
                    $rbamParameters->getButtons('revokeAll')['attributes'],
                    [
                        'type' => 'button',
                        '@click.prevent' => sprintf(
                            "\$dispatch('modal', %s)",
                            Json::encode([
                                'buttons' => [
                                    'continue' => [
                                        'href' => $urlGenerator->generate('rbam.user.assignment.revoke'),
                                        'data' => [],
                                    ],
                                ],
                                'closeDialog' => $translator->translate(id: 'label.close-dialog', category: 'rbam'),
                                'content' => $translator->translate(
                                    'message.user.assignment.revoke-all',
                                    [
                                        'user' => $user->getName(),
                                    ],
                                    'rbam'
                                ),
                                'title' => $translator->translate(
                                    id: 'header.user.assignment.revoke-all',
                                    category: 'rbam'
                                ),
                            ])
                        ),
                    ]
                )
            ),
            attributes: ['class' => 'toolbar']
        )
            ->render()
        : ''
    )
    ->noResultsText($translator->translate(id: 'message.role.none-assigned'))
    ->columns(
        new DataColumn(
            header: $translator->translate(id: 'label.name'),
            content: static fn (Item $item) => $translator->translate(
                id: $item->getItem()->getName(),
                category: 'rbac'
            ),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.description'),
            content: static fn (Item $item) => $translator->translate(
                id: $item->getItem()->getDescription(),
                category: 'rbac'
            ),
        ),
        new ActionColumn(
            content: static fn ($data) => array_reduce(
                $assignments,
                fn(bool $carry, Assignment $assignment)
                    => $carry || $assignment->getItemName() === $data->getItem()->getName()
                ,
                false
            )
                ? Html::button(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('revoke')['content'],
                        category: 'rbam'
                    ),
                    attributes: array_merge(
                        $rbamParameters->getButtons('revoke')['attributes'],
                        [
                            'type' => 'button',
                            '@click.prevent' => sprintf(
                                "\$dispatch('modal', %s)",
                                Json::encode([
                                    'buttons' => [
                                        'continue' => [
                                            'href' => $urlGenerator->generate('rbam.user.assignment.revoke'),
                                            'data' => [
                                                'item' => $data->getItem()->getName(),
                                            ],
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate(id: 'label.close-dialog', category: 'rbam'),
                                    'content' => $translator->translate(
                                        'message.user.assignment.revoke',
                                        [
                                            'item' => $data->getItem()->getName(),
                                            'user' => $user->getName(),
                                        ],
                                        'rbam'
                                    ),
                                    'title' => $translator->translate(
                                        'header.user.assignment.revoke',
                                        [
                                            'item' => $data->getItem()->getName(),
                                        ],
                                        'rbam'
                                    ),
                                ])
                            )
                        ]
                    )
                )
                : ''
            ,
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ],
            visible: $currentUser->can(RbamPermission::userUpdate->getItemName())
        ),
    )
;