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
use Yiisoft\Json\Json;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($translator->translate('label.rbam'));
$this->registerJs(sprintf('rbam = new Rbam("rbam")'));

$breadcrumbs = [
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<div id="rbam" data-_csrf="<?=$csrf?>">
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

    <div x-data="{ active: null }" class="mx-auto min-h-[16rem] w-full max-w-3xl">
        <div x-data="{
            id: 1,
            get expanded() {
                return this.active === this.id
            },
            set expanded(value) {
                this.active = value ? this.id : null
            },
        }" role="region" class="block border-b border-gray-800/10 pb-4 pt-4 first:pt-0 last:border-b-0 last:pb-0">
            <h2>
                <button
                    type="button"
                    x-on:click="expanded = !expanded"
                    :aria-expanded="expanded"
                    class="group flex w-full items-center justify-between text-left font-medium text-gray-800"
                >
                    <span class="flex-1">Actions</span>

                    <!-- Heroicons mini chevron-up -->
                    <svg x-show="expanded" x-cloak class="size-5 shrink-0 text-gray-300 group-hover:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.47 6.47a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10 8.06l-3.72 3.72a.75.75 0 0 1-1.06-1.06l4.25-4.25Z" clip-rule="evenodd"></path>
                    </svg>

                    <!-- Heroicons mini chevron-down -->
                    <svg x-show="!expanded" class="size-5 shrink-0 text-gray-300 group-hover:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" data-slot="icon">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </h2>

            <div x-show="expanded" x-collapse>
                <div class="pt-2 text-gray-600 max-w-xl">
                    Clear RBAC Assignments, Permission, and Roles
                </div>
                <div>
                    <?= Html::button(
                        content: $translator->translate($rbamParameters->getButtons('clear')['content']),
                        attributes: array_merge(
                            $rbamParameters->getButtons('clear')['attributes'],
                            [
                                'type' => 'button',
                                '@click' => sprintf(
                                   "\$dispatch('modal', %s)",
                                    Json::encode([
                                        'buttons' => [
                                            'continue' => [
                                                'href' => $urlGenerator->generate('rbam.clear'),
                                            ]
                                        ],
                                        'closeDialog' => $translator->translate('label.close-dialog'),
                                        'content' => $translator->translate('message.rbac.clear'),
                                        'title' => $translator->translate('header.rbac.clear'),
                                    ])
                                ),
                            ]
                        )
                    )
                        ->render()
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>