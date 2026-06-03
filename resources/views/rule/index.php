<?php

declare(strict_types=1);

/**
 * @var string $csrf
 * @var int $currentPage
 * @var CurrentUser $currentUser
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var RuleInterface[] $rules
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\PaginatorUrlCreator;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\Rule\RuleInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Json\Json;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
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

$this->setTitle($translator->translate(id: 'label.rules', category: 'rbam'));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
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
    ->header($translator->translate(id: 'label.rules', category: 'rbam'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes(['class' => 'grid-body'])
    ->layout("{header}\n{toolbar}\n{summary}\n{items}\n{pager}")
    ->toolbar($currentUser->can(RbamPermission::ruleCreate->getItemName())
        ? Html::div(Html::a(
            content: $translator->translate(id: 'button.rule.create', category: 'rbam'),
            url: $urlGenerator->generate('rbam.rule.create'),
            attributes: $rbamParameters->getButtons('createRule')['attributes']
        ))
            ->class('toolbar')
            ->render()
        : ''
    )
    ->noResultsText($translator->translate(id: 'message.rule.none-found', category: 'rbam'))
    ->columns(
        new DataColumn(
            header: $translator->translate(id: 'label.name', category: 'rbam'),
            content: static fn (RuleInterface $rule) => $rule->getName(),
            filter: true,
            filterFactory: LikeFilterFactory::class,
            filterEmpty: true,
            bodyClass: 'name',
        ),
        new DataColumn(
            header: $translator->translate(id: 'label.description', category: 'rbam'),
            content: static fn (RuleInterface $rule) => $translator->translate(
                id: $rule->getDescription(),
                category: 'rbac-rule'
            ),
            bodyClass: 'description',
        ),
        new ActionColumn(
            template: '{view}{update}{delete}',
            urlCreator: static fn (string $action, DataContext $context) => $urlGenerator->generate(
                sprintf('rbam.rule.%s', $action),
                [
                    'name' => $context->key,
                ]
            ),
            buttons: [
                'delete' => static fn (string $url, DataContext $context) => Html::button(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('remove')['content'],
                        category: 'rbam'
                    ),
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
                                                'rule' => $context->key,
                                            ]
                                        ],
                                    ],
                                    'closeDialog' => $translator->translate(id: 'label.close-dialog'),
                                    'content' => $translator->translate(
                                        'message.rule.remove',
                                        [
                                            'rule' => $context->key,
                                        ],
                                        'rbam'
                                    ),
                                    'title' => $translator->translate(
                                        'header.rule.remove',
                                        [
                                            'rule' => $context->key,
                                        ],
                                        'rbam'
                                    ),
                                ])
                            ),
                        ]
                    ))
                    ->render()
                ,
                'update' => new ActionButton(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('update')['content'],
                        category: 'rbam'
                    ),
                    attributes: $rbamParameters->getButtons('update')['attributes'],
                ),
                'view' => new ActionButton(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('view')['content'],
                        category: 'rbam'
                    ),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            visibleButtons: [
                'delete' => $currentUser->can(RbamPermission::ruleDelete->getItemName()),
                'update' => $currentUser->can(RbamPermission::ruleUpdate->getItemName()),
                'view' => $currentUser->can(RbamPermission::ruleView->getItemName()),
            ],
            bodyAttributes: [
                'class' => 'action',
                'x-data' => true
            ]
        )
    )
?>