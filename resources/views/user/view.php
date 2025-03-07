<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Assignment[] $assignments
 * @var AssetManager $assetManager
 * @var ItemsStorageInterface $itemsStorage
 * @var Permission[] $permissionsGranted
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var Role[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Assets\AssetManager;
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
use Yiisoft\Yii\View\Renderer\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$this->setTitle($user->getName());

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.users'),
        'url' => $urlGenerator->generate('rbam.userIndex'),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h2><?= Html::encode($this->getTitle()) ?></h2>

<div id="js-items" data-csrf="<?= $csrf ?>" data-item="<?= $user->getId() ?>">
    <?= $this->render(
        '_assignments',
        [
            'assignments' => $assignments,
            'assignedRoles' => $assignedRoles,
            'itemsStorage' => $itemsStorage,
            'permissionsGranted' => $permissionsGranted,
            'rbamParameters' => $rbamParameters,
            'translator' => $translator,
            'unassignedRoles' => $unassignedRoles,
            'urlGenerator' => $urlGenerator,
        ]
    ) ?>
</div>