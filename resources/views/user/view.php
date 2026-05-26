<?php

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Assignment[] $assignments
 * @var AssetManager $assetManager
 * @var CurrentUser $currentUser
 * @var Permission[] $permissionsGranted
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var Role[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\User\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('const rbam = new Rbam("user-view");');

$this->setTitle($user->getName());

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.users', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.user.index'),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);

echo Html::div()
    ->attributes([
        'data-_csrf' => $csrf,
        'data-user' => $user->getId(),
        'id' => 'user-view',
    ])
    ->open()
;

echo Html::h2($this->getTitle());

echo $this->render(
    '_assignedRoles',
    [
        'assignedRoles' => $assignedRoles,
        'assignments' => $assignments,
        'currentUser' => $currentUser,
        'user' => $user,
    ]
);

echo $this->render(
    '_unassignedRoles',
    [
        'currentUser' => $currentUser,
        'unassignedRoles' => $unassignedRoles,
        'user' => $user,
    ]
);

echo $this->render(
    '../item/_items',
    [
        'actionButtons' => ['view'],
        'currentUser' => $currentUser,
        'header' => 'label.permissions.granted',
        'item' => null,
        'items' => $permissionsGranted,
        'noResultsText' => 'message.permission.none-granted',
        'paginationUrl' => $urlGenerator->generate('rbam.user.permissions'),
        'toolbar' => '',
        'translator' => $translator,
        'type' => Item::TYPE_PERMISSION,
        'urlGenerator' => $urlGenerator,
        'user' => $user
    ]
);

echo Html::div()->close();