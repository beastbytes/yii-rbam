<?php

declare(strict_types=1);

/**
 * @var Csrf $csrf
 * @var CurrentUser $currentUser
 * @var int $permissions
 * @var RbamParameters $rbamParameters
 * @var int $roles
 * @var int $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var int $users
 */

use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($translator->translate(id: 'label.rbam', category: 'rbam'));
$this->registerJs(sprintf('rbam = new Rbam("rbam")'));

$breadcrumbs = [
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<div id="rbam" data-_csrf="<?=$csrf?>">
    <div class="cards">
        <?php foreach ([Item::TYPE_PERMISSION, Item::TYPE_ROLE] as $type): ?>
            <?php $types = $type . 's'; ?>
            <div class="card <?= $type ?>">
                <div class="card-header">
                    <span><?= $translator->translate(id: sprintf('label.%s', $types), category: 'rbam') ?>
                        <span class='badge'><?= $$types ?></span>
                    </span>
                </div>
                <div class="card-body">
                    <?php if($currentUser->can(RbamPermission::itemView->getItemName())): ?>
                    <a class="btn btn_manage" href="<?= $urlGenerator->generate(
                        'rbam.item.index',
                        ['type' => $types]
                    ) ?>">
                        <?= $translator->translate(id: sprintf('label.%s.manage', $types), category: 'rbam') ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="card rules">
            <div class="card-header">
                <span><?= $translator->translate(id: 'label.rules', category: 'rbam')?>
                    <span class='badge'><?= $rules ?></span>
                </span>
            </div>
            <div class="card-body">
                <?php if($currentUser->can(RbamPermission::ruleView->getItemName())): ?>
                <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.rule.index') ?>">
                    <?= $translator->translate(id: 'label.rules.manage', category: 'rbam')?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card users">
            <div class="card-header">
                <span><?= $translator->translate(id: 'label.users', category: 'rbam')?>
                    <span class='badge'><?= $users ?></span>
                </span>
            </div>
            <div class="card-body">
                <?php if($currentUser->can(RbamPermission::userView->getItemName())): ?>
                <a class="btn btn_manage" href="<?= $urlGenerator->generate('rbam.user.index') ?>">
                    <?= $translator->translate(id: 'label.users.manage', category: 'rbam')?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
if ($currentUser->can(RbamPermission::clear->getItemName())):
    $this->setBlock(
        'block-menu',
        '<div x-data x-menu class="header-menu">
            <button x-menu:button>
                <span class="sr-only">' . $translator->translate(id: 'label.menu', category: 'rbam') . '</span>
    
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100% 100%">
                    <path xmlns="http://www.w3.org/2000/svg" d="M5 5H18 5" stroke="#000000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path xmlns="http://www.w3.org/2000/svg" d="M5 11L18 11" stroke="#000000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path xmlns="http://www.w3.org/2000/svg" d="M5 17L18 17" stroke="#000000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <ul
                x-menu:items
                x-transition.origin.top.left
                x-cloak
            >'
            . Html::li(
                content: $translator->translate(id: 'label.menu.clear', category: 'rbam'),
                attributes: [
                    'x-menu:item' => true,
                    '@click' => sprintf(
                        "\$dispatch('modal', %s)",
                        Json::encode([
                            'buttons' => [
                                'continue' => [
                                    'href' => $urlGenerator->generate('rbam.clear'),
                                ]
                            ],
                            'closeDialog' => $translator->translate(id: 'label.close-dialog', category: 'rbam'),
                            'content' => $translator->translate(id: 'message.rbac.clear', category: 'rbam'),
                            'title' => $translator->translate(id: 'header.rbac.clear', category: 'rbam'),
                        ])
                    ),
                ]
            )
            . '</ul>
        </div>'
    );
endif;