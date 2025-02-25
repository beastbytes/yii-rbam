<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var int $currentPage
 * @var Inflector $inflector
 * @var int $pageSize
 * @var RbamParameters $rbamParameters
 * @var array $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\RbamRuleInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle($translator->translate('label.rules'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= GridView::widget()
    ->dataReader(
        (new OffsetPaginator(new IterableDataReader($rules)))
            ->withCurrentPage($currentPage)
            ->withPageSize($pageSize)
    )
    ->containerAttributes(['class' => 'grid-view rules'])
    ->header($translator->translate('label.rules'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{summary}\n{items}\n{pager}")
    ->toolbar(
        Html::a(
            content: $translator->translate('button.create-rule'),
            url: $urlGenerator->generate('rbam.createRule'),
            attributes: $rbamParameters->getButtons('createRule')['attributes'],
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no-rules-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static function (RbamRuleInterface $rule) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $rule->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewRule',
                        ['name' => $inflector->toSnakeCase($rule->getName())]
                    )
                )
                    ->render()
                ;
            }
        ),
        new DataColumn(header: 'Description', content: static fn(RbamRuleInterface $rule) => $rule->getDescription()),
        new ActionColumn(
            template: '{view}{update}',
            urlCreator: static function($action, $context) use ($urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Rule', [
                    'name' => strtolower($context->key)
                ]);
            },
            buttons: [
                'update' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('update')['content']),
                    attributes: $rbamParameters->getButtons('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: ['class' => 'action'],
        )
    )
?>

<?= Html::a(
    $translator->translate($rbamParameters->getButtons('done')['content']),
    $urlGenerator->generate('rbam.rbam'),
    $rbamParameters->getButtons('done')['attributes']
)
    ->render()
?>