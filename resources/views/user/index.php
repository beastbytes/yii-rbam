<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var int $currentPage
 * @var Inflector $inflector
 * @var ManagerInterface $rbacManager
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 * @var WebView $this
 */

use BeastBytes\Yii\Dataview\Assets\PaginationAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$assetManager->register(PaginationAsset::class);

$this->setTitle($translator->translate('label.users'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= GridView::widget()
    ->dataReader(
        (new OffsetPaginator(new IterableDataReader($users)))
            ->withCurrentPage($currentPage)
            ->withPageSize($rbamParameters->getPageSize())
    )
    ->containerAttributes(['class' => 'grid-view users', 'id' => 'users'])
    ->header($this->getTitle())
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->emptyText($translator->translate('message.no-users-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(UserInterface $user) => $user->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.roles'),
            content: static function(UserInterface $user) use ($rbacManager) {
                return count($rbacManager->getRolesByUserId($user->getId()));
            },
            bodyClass: 'number',
        ),
        new DataColumn(
            header: $translator->translate('label.permissions'),
            content: static function(UserInterface $user) use ($rbacManager) {
                return count($rbacManager->getPermissionsByUserId($user->getId()));
            },
            bodyClass: 'number',
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static function($action, $context) use ($urlGenerator)
            {
                return $urlGenerator->generate('rbam.viewUser', [
                    'id' => $context->data->getId()
                ]);
            },
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: ['class' => 'action'],
        )
    )
?>