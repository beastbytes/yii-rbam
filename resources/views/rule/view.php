<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var RbamRuleInterface $rule
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
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
    ->fieldTemplate('{label}{value}')
    ->data($rule)
    ->fields(
        new DataField(
            label: $translator->translate('label.name'),
            value: fn($rule) => $rule->getName(),
        ),
        new DataField(
            label: $translator->translate('label.description'),
            value: fn($rule) => $rule->getDescription(),
        ),
        new DataField(
            label: $translator->translate('label.code'),
            value: fn($rule) => '<pre><code>'
                . 'public function execute(?string $userId, Item $item, RuleContext $context): bool'
                . "\n{\n"
                . $rule->getCode()
                . "\n}</code></pre>"
            ,
            encodeValue: false,
        ),
    )
    ->header(
        Html::a(
            $translator->translate('button.update', [
                'name' => $rule->getName(),
            ]),
            $urlGenerator->generate('rbam.updateRule', [
                'name' => $inflector->toSnakeCase($rule->getName()),
            ]),
            ['class' => 'btn btn_update']
        )
        ->render()
    )
    ->render()
?>

<?= Html::a(
    $translator->translate($rbamParameters->getButtons('done')['content']),
    $urlGenerator->generate('rbam.ruleIndex'),
    $rbamParameters->getButtons('done')['attributes']
)
    ->render()
?>