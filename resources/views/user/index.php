<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var int $currentPage
 * @var Inflector $inflector
 * @var int $pageSize
 * @var ManagerInterface $rbacManager
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\OffsetPagination;
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;

$this->setTitle($translator->translate('label.users'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->urlCreator(new UrlCreator($urlGenerator))
    ->dataReader(
        (new OffsetPaginator(new IterableDataReader($users)))
            ->withCurrentPage($currentPage)
            ->withPageSize($pageSize)
    )
    ->containerAttributes(['class' => 'grid_view users'])
    ->header($translator->translate('label.users'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{summary}\n{items}\n{pager}")
    ->emptyText($translator->translate('message.no_users_found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(UserInterface $user) => $user->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.roles'),
            bodyAttributes: ['class' => 'number'],
            content: static function(UserInterface $user) use ($rbacManager) {
                return count($rbacManager->getRolesByUserId($user->getId()));
            },
        ),
        new DataColumn(
            header: $translator->translate('label.permissions'),
            bodyAttributes: ['class' => 'number'],
            content: static function(UserInterface $user) use ($rbacManager) {
                return count($rbacManager->getPermissionsByUserId($user->getId()));
            }
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
        )
    )
?>
