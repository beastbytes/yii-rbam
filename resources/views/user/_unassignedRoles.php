<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Assignment[] $assignments
 * @var Csrf $csrf
 * @var ?int $currentPage
 * @var CurrentUser $currentUser
 * @var Permission[] $permissionsGranted
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var RbamItem[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\Alpine\Modal\Modal;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\User\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Permission;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
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
    ->noResultsText($translator->translate(id: 'message.role.none-unassigned'))
    ->header($translator->translate(id: 'label.role.unassigned'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->columns(
        new DataColumn(
            header: $translator->translate(id: 'label.name'),
            content: static fn (RbamItem $role) => $translator->translate(
                id: $role->getItem()->getName(),
                category: 'rbac'
            ),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.description'),
            content: static fn (RbamItem $role) => $translator->translate(
                id: $role->getItem()->getDescription(),
                category: 'rbac'
            ),
        ),
        new ActionColumn(
            template: '{assign}',
            urlCreator: static fn (string $action, DataContext $context) => $urlGenerator->generate(
                'rbam.user.assign-role',
            ),
            buttons: [
                'assign' => static fn (string $url, DataContext $context) => (new Modal($assetManager))
                    ->button(
                        Html::button(
                            content: $translator->translate(id: 'button.continue', category: 'rbam'),
                            attributes: [
                                'class' => 'btn btn_continue',
                                '@click' => sprintf(
                                    "rbam.action({href: '%s', data: %s})",
                                    $url,
                                    Json::encode(['item' => $context->data->getItem()->getName()]),
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
                        'message.user.assignment.revoke',
                        [
                            'item' => $context->data->getItem()->getName(),
                            'user' => $user->getName(),
                        ],
                        'rbam'
                    ))
                    ->title($translator->translate(
                        'header.user.assignment.revoke',
                        [
                            'item' => $context->data->getItem()->getName(),
                        ],
                        'rbam'
                    ))
                    ->trigger(Html::button(
                        content: $translator->translate(
                            id: $rbamParameters->getButtons('assign')['content'],
                            category: 'rbam'
                        ),
                        attributes: array_merge(
                            $rbamParameters->getButtons('assign')['attributes'],
                            [
                                'type' => 'button',
                            ]
                        )
                    ))
                    ->render()
            ],
            visibleButtons: [
                'assign' => static fn (RbamItem $data) => !$data->isGuestRole(),
            ],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ],
            visible: $currentUser->can(RbamPermission::userUpdate->getItemName())
        ),
    )
;