<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var CurrentUser $currentUser
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var RuleInterface $rule
 * @var RbamItem[] $roles
 * @var RbamItem[] $permissions
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Alpine\Tabs\Tabs;
use BeastBytes\Yii\Rbam\Asset\PrismAsset;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\DetailView\GetValueContext;

$assetManager->register(PrismAsset::class);

$this->setTitle($translator->translate('label.rule.name', ['name' => $rule->getName()], 'rbam'));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.rules', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rule.index'),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

echo DetailView::widget()
    ->containerAttributes(['class' => 'detail-view rule'])
    ->containerTag('div')
    ->fieldTemplate('{label}{value}')
    ->data($rule)
    ->fields(
        new DataField(
            label: $translator->translate(id: 'label.name', category: 'rbam'),
            value: static fn (GetValueContext $context) => $context->data->getName(),
            fieldAttributes: ['class' => 'name'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.description.raw', category: 'rbam'),
            value: static fn (GetValueContext $context) => $context->data->getDescription(),
            fieldAttributes: ['class' => 'description'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.description', category: 'rbam'),
            value: static fn (GetValueContext $context) => $translator->translate(
                id: $context->data->getDescription(),
                category: 'rbac-rule',
            ),
            fieldAttributes: ['class' => 'description'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.code'),
            value: static fn (GetValueContext $context) => sprintf(<<<'RULE'
<pre><code class="language-php">
public function execute(?string $userId, Permission $item, RuleContext $context): bool
{
%s
}</code></pre>
RULE,
                $context->data->getCode()
            ),
            valueEncode: false,
            fieldAttributes: ['class' => 'code'],
        ),
    )
    ->prepend($currentUser->can(RbamPermission::ruleUpdate->getItemName())
        ? Html::a(
            content: $translator->translate('button.update', ['item' => $rule->getName()], 'rbam'),
            url: $urlGenerator->generate('rbam.rule.update', ['name' => $rule->getName()]),
            attributes: $rbamParameters->getButtons('update')['attributes']
        )
        . Html::a(
            content: $translator->translate(id: 'button.translations', category: 'rbam'),
            url: $urlGenerator->generate('rbam.rule.translate', ['name' => $rule->getName()]),
            attributes: $rbamParameters->getButtons('translate')['attributes']
        )
        : ''
    )
    ->render()
;

echo Tabs::widget([$assetManager])
    ->tabs(
        [
            'tab' => $translator->translate(id: 'label.roles', category: 'rbam'),
            'panel' => $this->render(
                '../item/_items',
                [
                    'actionButtons' => ['view'],
                    'currentUser' => $currentUser,
                    'noResultsText' => 'message.role.none-found',
                    'header' => '',
                    'item' => null,
                    'items' => $roles,
                    'paginationUrl' => $urlGenerator->generate('rbam.rule.items'),
                    'toolbar' => '',
                    'translator' => $translator,
                    'type' => Item::TYPE_ROLE,
                    'urlGenerator' => $urlGenerator,
                    'user' => null,
                ]
            )
        ],
        [
            'tab' => $translator->translate(id: 'label.permissions', category: 'rbam'),
            'panel' => $this->render(
                '../item/_items',
                [
                    'actionButtons' => ['view'],
                    'currentUser' => $currentUser,
                    'noResultsText' => 'message.permission.none-found',
                    'header' => '',
                    'item' => null,
                    'items' => $permissions,
                    'paginationUrl' => $urlGenerator->generate('rbam.rule.items'),
                    'toolbar' => '',
                    'translator' => $translator,
                    'type' => Item::TYPE_PERMISSION,
                    'urlGenerator' => $urlGenerator,
                    'user' => null,
                ]
            ),
        ],
    )
;