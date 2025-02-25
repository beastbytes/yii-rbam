<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Permission[] $permissions
 * @var Role[] $roles
 * @var RbamRuleInterface[] $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $users
 */

use BeastBytes\Yii\Rbam\RbamRuleInterface;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate('label.rbam'));

$breadcrumbs = [
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<div class="cards">
    <?php foreach ([Item::TYPE_ROLE, Item::TYPE_PERMISSION] as $type): ?>
        <?php $type = $type . 's'; ?>
        <div class="card <?= $type ?>">
            <div class="card-header">
                <span><?= $translator->translate('label.' . $type) ?><span class='badge'><?= count($$type) ?></span></span>
            </div>
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
        <div class="card-header">
            <span><?= $translator->translate('label.rules')?><span class='badge'><?= count($rules) ?></span></span>
        </div>
        <div class="card-body">
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.ruleIndex') ?>">
                <?= $translator->translate('label.manage-rules')?>
            </a>
        </div>
    </div>
    <div class="card users">
        <div class="card-header">
            <span><?= $translator->translate('label.users')?><span class='badge'><?= count($users) ?></span></span>
        </div>
        <div class="card-body">
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.userIndex') ?>">
                <?= $translator->translate('label.manage-users')?>
            </a>
        </div>
    </div>
</div>