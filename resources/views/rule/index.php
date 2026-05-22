<?php

declare(strict_types=1);

/**
 * @var string $csrf
 * @var int $currentPage
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var RuleInterface[] $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory;
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

$this->registerJs('paginators.push(new Paginator("rules", ".grid-view nav a"));');
$this->registerJs('rbam = new Rbam("rules")');

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
    ->containerAttributes([
        'class' => 'grid-view rules',
        'data-_csrf' => $csrf,
        'id' => 'rules'
    ])
    ->containerTag('div')
    ->dataReader(
        (new OffsetPaginator(new IterableDataReader($rules)))
            ->withCurrentPage($currentPage ?? 1)
            ->withPageSize($rbamParameters->getPageSize())
    )
    ->paginationWidget(OffsetPagination::widget())
    ->urlCreator(new PaginatorUrlCreator($urlGenerator->generate('rbam.rule.index')))
    ->header($translator->translate('label.rules'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{toolbar}\n{summary}\n{items}\n{pager}")
    ->toolbar(Html::div(Html::a(
            content: $translator->translate('button.rule.create'),
            url: $urlGenerator->generate('rbam.rule.create'),
            attributes: $rbamParameters->getButtons('createRule')['attributes']
        ))
        ->class('toolbar')
        ->render()
    )
    ->noResultsText($translator->translate('message.rule.none-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(RuleInterface $rule) => $rule->getName(),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
        ),
        new DataColumn(
            header: 'Description',
            content: static fn(RuleInterface $rule) => $rule->getDescription(),
        ),
        new ActionColumn(
            template: '{view}{update}{remove}',
            urlCreator: static fn(string $action, DataContext $context) => $urlGenerator->generate(
                sprintf('rbam.rule.%s', $action),
                [
                    'name' => $context->data->getName(),
                ]
            ),
            buttons: [
                'remove' => static fn(string $url, DataContext $context) => Html::button(
                    content: $translator->translate($rbamParameters->getButtons('remove')['content']),
                    attributes: array_merge(
                        $rbamParameters->getButtons('remove')['attributes'],
                        [
                            'type' => 'button',
                            '@click' => sprintf(
                                "\$dispatch('modal', %s)",
                                Json::encode([
                                    'buttons' => [
                                        'continue' => [
                                            'href' => $url,
                                            'data' => [
                                                'rule' => substr(
                                                    $url,
                                                    strpos($url, '/', 7) + 1,
                                                    strrpos($url, '/')
                                                        - strpos($url, '/', 7) - 1
                                                ),
                                            ]
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate('label.close-dialog'),
                                    'content' => $translator->translate(
                                        'message.rule.remove',
                                        [
                                            'rule' => substr(
                                                $url,
                                                strpos($url, '/', 7) + 1,
                                                strrpos($url, '/')
                                                    - strpos($url, '/', 7) - 1
                                            ),
                                        ]
                                    ),
                                    'title' => $translator->translate(
                                        'header.rule.remove',
                                        [
                                            'rule' => substr(
                                                $url,
                                                strpos($url, '/', 7) + 1,
                                                strrpos($url, '/')
                                                    - strpos($url, '/', 7) - 1
                                            ),
                                        ]
                                    ),
                                ])
                            ),
                        ]
                    ))
                    ->render()
                ,
                'update' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('update')['content']),
                    attributes: $rbamParameters->getButtons('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ],
        )
    )
?>