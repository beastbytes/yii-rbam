<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Inflector $inflector
 * @var RbamRuleInterface $rule
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamRuleInterface;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;

$this->setTitle($translator->translate(
    'label.rule-name',
    ['name' => $rule->getName()]
));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.rules'),
        'url' => $urlGenerator->generate('rbam.ruleIndex'),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= DetailView::widget()
    ->attributes(['class' => 'detail-view rule'])
    ->data($rule)
    ->fields(
        new DataField(
            label: $translator->translate('label.name'),
            value: fn($rule) => $rule->getName(),
            valueTag: 'span',
        ),
        new DataField(
            label: $translator->translate('label.description'),
            value: fn($rule) => $rule->getDescription(),
            valueTag: 'span',
        ),
        new DataField(
            label: $translator->translate('label.code'),
            value: fn($rule) => $rule->getCode(),
            valueTag: 'pre',
        ),
    )
    ->header(
        Html::a(
            $translator->translate('button.update-rule', [
                'name' => $rule->getName(),
            ]),
            $urlGenerator->generate('rbam.updateRule', [
                'name' => $inflector->toSnakeCase($rule->getName()),
            ])
        )
        ->render()
    )
    ->render()
?>