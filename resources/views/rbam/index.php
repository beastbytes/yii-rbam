<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Permission[] $permissions
 * @var Role[] $roles
 * @var RbamRuleInterface[] $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $users
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\RbamRuleInterface;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$assetManager->register(RbamAsset::class);
$this->addCssFiles($assetManager->getCssFiles());

$this->setTitle($translator->translate('title.rbam'));

$breadcrumbs = [
    $translator->translate('label.rbam')
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>
<div class="rbam">
    <?php foreach ([Item::TYPE_ROLE, Item::TYPE_PERMISSION] as $type): ?>
        <?php $type = $type . 's'; ?>
        <div class="card <?= $type ?>">
            <header class="card-header">
                <?= $translator->translate('label.' . $type) ?><div class="badge"><?= count($$type) ?></div>
            </header>
            <div class="card-body">
                <a class="btn btn_manage" href="<?= $urlGenerator->generate(
                    'rbam.itemIndex',
                    ['type' => $type]
                ) ?>">
                    <?= $translator->translate('label.manage-' . $type) ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="card rules">
        <header class="card-header">
            <?= $translator->translate('label.rules')?><div class="badge"><?= count($rules) ?></div>
        </header>
        <div class="card-body">
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.ruleIndex') ?>">
                <?= $translator->translate('label.manage-rules')?>
            </a>
        </div>
    </div>
    <div class="card users">
        <header class="card-header">
            <?= $translator->translate('label.users')?><div class="badge"><?= count($users) ?></div>
        </header>
        <div class="card-body">
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.userIndex') ?>">
                <?= $translator->translate('label.manage-users')?>
            </a>
        </div>
    </div>
</div>