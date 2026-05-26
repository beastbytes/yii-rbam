<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Assignment[] $assignments
 * @var CurrentUser $currentUser
 * @var Role[] $children
 * @var int $currentPage
 * @var MermaidHierarchyDiagram $diagram
 * @var Inflector $inflector
 * @var Item $item
 * @var Permission[] $permissions
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Mermaid\Mermaid;
use BeastBytes\Yii\Rbam\Alpine\Tabs;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\DetailView\GetValueContext;
use Yiisoft\Yii\DataView\DetailView\ValueContext;

$this->registerScriptTag(
    Html::script()
        ->type('module')
        ->content(Mermaid::js(['startOnLoad' => true]))
);

$css = '';
foreach ($rbamParameters->getMermaidDiagramStyles() as $class => $styles):
    foreach ($styles as $element => $attributes):
        $css .= "#mermaid g.$class $element {";
        foreach ($attributes as $attribute => $style):
            $css .= "$attribute:$style;";
        endforeach;
        $css .= "}\n";
    endforeach;
endforeach;
$this->registerCss($css);

$this->setTitle($item->getName());

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.roles', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.item.index', ['type' => 'roles']),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

echo DetailView::widget()
    ->containerAttributes(['class' => 'detail-view role'])
    ->containerTag('div')
    ->fieldTemplate('{label}{value}')
    ->data($item)
    ->fields(
        new DataField(
            label: $translator->translate(id: 'label.name'),
            value: static fn (GetValueContext $context) => $translator->translate(
                id: $context->data->getName(),
                category: 'rbac'
            ),
        ),
        new DataField(
            label: $translator->translate(id: 'label.type', category: 'rbam'),
            value: $translator->translate(id: 'label.role', category: 'rbam'),
        ),
        new DataField(
            label: $translator->translate(id: 'label.description', category: 'rbam'),
            value: static fn (GetValueContext $context) => $translator->translate(
                id: $context->data->getDescription(),
                category: 'rbac'
            ),
        ),
        new DataField(
            label: $translator->translate(id: 'label.rule'),
            value: static fn (GetValueContext $context) => is_string($context->data->getRuleName())
                ? substr($context->data->getRuleName(), 30, -4)
                : $translator->translate(id: 'message.no-rule', category: 'rbam')
            ,
            valueAttributes: static fn (ValueContext $context) => $context->data->getRuleName() === null
                ? ['class' => 'no_rule']
                : []
        ),
        new DataField(
            label: $translator->translate(id: 'label.created-at', category: 'rbam'),
            value: static fn (GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
        new DataField(
            label: $translator->translate(id: 'label.updated-at', category: 'rbam'),
            value: static fn (GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
    )
    ->prepend(
        Html::div(
            $translator->translate('label.role.name', ['name' => $item->getName()], 'rbac'),
            ['class' => 'header']
        )
        . Html::div(Html::a(
                content: $translator->translate(id: 'button.update', category: 'rbam'),
                url: $urlGenerator->generate(
                    'rbam.item.update',
                    [
                        'name' => $item->getName(),
                        'type' => 'role',
                    ]
                ),
                attributes: $rbamParameters->getButtons('update')['attributes']
            ))
    )
    ->render()
;

echo Tabs::widget([
    'tabs' => [
        $translator->translate(id: 'label.diagram', category: 'rbam') => $diagram->render(),
        $translator->translate(id: 'label.child-roles', category: 'rbam') => $this->render(
            '_items',
            [
                'actionButtons' => ['view'],
                'currentUser' => $currentUser,
                'noResultsText' => 'message.role.none-found',
                'header' => '',
                'item' => $item,
                'items' => $children,
                'paginationUrl' => $urlGenerator->generate(
                    'rbam.item.child-items',
                    [
                        'name' => $item->getName(),
                        'type' => Item::TYPE_ROLE,
                    ]
                ),
                'toolbar' => Html::div(Html::a(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('manageChildRoles')['content'],
                        category: 'rbam'
                    ),
                    url: $urlGenerator->generate(
                        'rbam.item.manage-children',
                        [
                            'childType' => Item::TYPE_ROLE,
                            'name' => $item->getName(),
                            'type' => Item::TYPE_ROLE,
                        ]
                    ),
                    attributes: $rbamParameters->getButtons('manageChildRoles')['attributes']
                )),
                'translator' => $translator,
                'type' => Item::TYPE_ROLE,
                'urlGenerator' => $urlGenerator,
                'user' => null,
            ]
        ),
        $translator->translate(id: 'label.permissions') => $this->render(
            '_items',
            [
                'actionButtons' => ['view'],
                'currentUser' => $currentUser,
                'noResultsText' => 'message.permission.none-found',
                'header' => '',
                'item' => $item,
                'items' => $permissions,
                'paginationUrl' => $urlGenerator->generate(
                    'rbam.item.child-items',
                    [
                        'name' => $item->getName(),
                        'type' => Item::TYPE_PERMISSION,
                    ]
                ),
                'toolbar' => Html::div(Html::a(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('managePermissions')['content'],
                        category: 'rbam'
                    ),
                    url: $urlGenerator->generate(
                        'rbam.item.manage-children',
                        [
                            'childType' => Item::TYPE_PERMISSION,
                            'name' => $item->getName(),
                            'type' => Item::TYPE_ROLE,
                        ]
                    ),
                    attributes: $rbamParameters->getButtons('managePermissions')['attributes']
                )),
                'translator' => $translator,
                'type' => Item::TYPE_PERMISSION,
                'urlGenerator' => $urlGenerator,
                'user' => null,
            ]
        ),
        $translator->translate(id: 'label.assignments') => $this->render(
            '_assignments',
            [
                'assignments' => $assignments,
                'item' => $item,
                'translator' => $translator,
                'urlGenerator' => $urlGenerator,
            ]
        ),
    ],
]);