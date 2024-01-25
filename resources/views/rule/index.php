<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var array $rules
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\RuleInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle(
    $translator->translate('title.rules')
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($rules))
    ->tableAttributes(['class' => 'grid_view rules'])
    ->layout("{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::a(
                content: $translator->translate('button.add_rule'),
                url: $urlGenerator->generate('rbam.addRule'),
                attributes: $rbamParameters->getActionButton('addRole')['attributes'],
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_rules_found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static function (RuleInterface $rule) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $rule->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewRule',
                        ['name' => $inflector->toSnakeCase($rule->getName())]
                    )
                )
                ->render();
            }
        ),
        new DataColumn(header: 'Description', content: static fn(RuleInterface $rule) => $rule->getDescription()),
        new ActionColumn(
            template: '{view} {update}',
            urlCreator: static function($action, $context) use ($urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Rule', [
                    'name' => strtolower($context->key)
                ]);
            },
            buttons: [
                'update' => new ActionButton(
                    content: $translator->translate($rbamParameters->getActionButton('update')['content']),
                    attributes: $rbamParameters->getActionButton('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getActionButton('view')['content']),
                    attributes: $rbamParameters->getActionButton('view')['attributes'],
                ),
            ]
        )
    )
?>
