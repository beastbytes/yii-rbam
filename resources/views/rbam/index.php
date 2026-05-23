<?php

declare(strict_types=1);

/**
 * @var CurrentUser $currentUser
 * @var int $permissions
 * @var int $roles
 * @var int $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var int $users
 */

use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate('label.rbam'));

$breadcrumbs = [
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<div class="cards">
    <?php foreach ([Item::TYPE_ROLE, Item::TYPE_PERMISSION] as $type): ?>
        <?php $types = $type . 's'; ?>
        <div class="card <?= $type ?>">
            <div class="card-header">
                <span><?= $translator->translate(sprintf('label.%s', $types)) ?><span class='badge'><?= $$types ?></span></span>
            </div>
            <div class="card-body">
                <?php if($currentUser->can(RbamPermission::itemView->getItemName())): ?>
                <a class="btn btn_manage" href="<?= $urlGenerator->generate(
                    'rbam.item.index',
                    ['type' => $types]
                ) ?>">
                    <?= $translator->translate(sprintf('label.%s.manage', $types)) ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="card rules">
        <div class="card-header">
            <span><?= $translator->translate('label.rules')?><span class='badge'><?= $rules ?></span></span>
        </div>
        <div class="card-body">
            <?php if($currentUser->can(RbamPermission::ruleView->getItemName())): ?>
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.rule.index') ?>">
                <?= $translator->translate('label.rules.manage')?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card users">
        <div class="card-header">
            <span><?= $translator->translate('label.users')?><span class='badge'><?= $users ?></span></span>
        </div>
        <div class="card-body">
            <?php if($currentUser->can(RbamPermission::userView->getItemName())): ?>
            <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.user.index') ?>">
                <?= $translator->translate('label.users.manage')?>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>