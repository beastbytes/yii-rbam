<?php

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Assignment[] $assignments
 * @var ItemsStorageInterface $itemsStorage
 * @var Permission[] $permissionsGranted
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var Role[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\BodyRowContext;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$assignmentNames = array_keys($assignments);

echo $this->render(
    '_assignedRoles',
    [
        'assignments' => $assignments,
        'itemsStorage' => $itemsStorage,
        'roles' => $assignedRoles,
        'user' => $user,
    ]
);

echo $this->render(
    '_unassignedRoles',
    [
        'roles' => $unassignedRoles,
        'user' => $user,
    ]
);

echo $this->render(
    '../item/_items',
    [
        'actionButtons' => ['view'],
        'header' => 'label.permissions-granted',
        'emptyText' => 'message.no-permissions-granted',
        'item' => null,
        'items' => $permissionsGranted,
        'itemsStorage' => $itemsStorage,
        'toolbar' => '',
        'translator' => $translator,
        'type' => 'permission',
        'urlGenerator' => $urlGenerator,
        'user' => $user
    ]
);