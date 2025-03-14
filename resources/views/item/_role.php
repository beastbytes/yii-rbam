<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Assignment[] $assignments
 * @var int $currentPage
 * @var Inflector $inflector
 * @var Item $item
 * @var ItemsStorageInterface $itemsStorage
 * @var Permission[] $permissions
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var TranslatorInterface $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Widgets\Assets\TabsAsset;
use BeastBytes\Yii\Widgets\Tabs;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;

$assetManager->register(TabsAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

echo Tabs::widget(['data' => [
    $translator->translate('label.assignments') => $this->render(
        '_assignments',
        [
            'assignments' => $assignments,
            'item' => $item,
            'itemsStorage' => $itemsStorage,
            'translator' => $translator,
            'urlGenerator' => $urlGenerator,
            'users' => $users
        ]
    ),
    $translator->translate('label.child-roles') => $this->render(
        '_items',
        [
            'actionButtons' => ['view'],
            'emptyText' => 'message.no-child-roles-found',
            'header' => 'label.child-roles',
            'item' => $item,
            'items' => $roles,
            'itemsStorage' => $itemsStorage,
            'toolbar' => Html::a(
                content: $translator->translate($rbamParameters->getButtons('manageChildRoles')['content']),
                url: $urlGenerator->generate(
                    'rbam.childRoles',
                    ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_ROLE]
                ),
                attributes: $rbamParameters->getButtons('manageChildRoles')['attributes']
            )
                ->render()
            ,
            'translator' => $translator,
            'type' => Item::TYPE_ROLE,
            'urlGenerator' => $urlGenerator,
            'user' => null,
        ]
    ),
    $translator->translate('label.permissions') => $this->render(
        '_items',
        [
            'actionButtons' => ['view'],
            'emptyText' => 'message.no-permissions-found',
            'header' => 'label.permissions',
            'item' => $item,
            'items' => $permissions,
            'itemsStorage' => $itemsStorage,
            'toolbar' => Html::a(
                content: $translator->translate($rbamParameters->getButtons('managePermissions')['content']),
                url: $urlGenerator->generate(
                    'rbam.rolePermissions',
                    ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_PERMISSION]
                ),
                attributes: $rbamParameters->getButtons('managePermissions')['attributes']
            )
                ->render()
            ,
            'translator' => $translator,
            'type' => Item::TYPE_PERMISSION,
            'urlGenerator' => $urlGenerator,
            'user' => null,
        ]
    ),
    $translator->translate('label.diagram') => (new MermaidHierarchyDiagram(
        $item,
        $itemsStorage,
        $inflector,
        $translator,
        $urlGenerator)
    )->render(),
]]);