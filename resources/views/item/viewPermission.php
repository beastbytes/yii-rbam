<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var RbamItem[] $children
 * @var int $currentPage
 * @var CurrentUser $currentUser
 * @var MermaidHierarchyDiagram $diagram
 * @var RbamItem $item
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var RbamUser[] $users
 */

use BeastBytes\Mermaid\Mermaid;
use BeastBytes\Yii\Rbam\Alpine\Tabs;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\DTO\User as RbamUser;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Html\NoEncode;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
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

$this->setTitle($item->getItem()->getName());

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: 'label.permissions', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.item.index', ['type' => 'permissions']),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

echo DetailView::widget()
    ->containerAttributes(['class' => 'detail-view permission'])
    ->containerTag('div')
    ->fieldTemplate('{label}{value}')
    ->data($item)
    ->fields(
        new DataField(
            label: $translator->translate(id: 'label.name', category: 'rbam'),
            value: static fn (GetValueContext $context) => $translator->translate(
                id: $context->data->getItem()->getName(),
                category: 'rbac'
            ),
            fieldAttributes: ['class' => 'name'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.type', category: 'rbam'),
            value: $translator->translate(id: 'label.permission', category: 'rbam'),
            fieldAttributes: ['class' => 'type'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.description', category: 'rbam'),
            value: static fn (GetValueContext $context)
                => $translator->translate(id: $context->data->getItem()->getDescription(), category: 'rbac')
            ,
            fieldAttributes: ['class' => 'description'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.granted-by'),
            value: static function (GetValueContext $context) use ($translator) {
                $grantedBy = [];

                foreach ($context->data->getParents() as $parent) {
                    $grantedBy[] = $translator->translate(id: $parent->getName(), category: 'rbac');
                }

                return '<div>' . implode('</div><div>', $grantedBy) . '</div>';
            },
            valueEncode: false,
            fieldAttributes: ['class' => 'granted-by'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.rule'),
            value: static fn (GetValueContext $context) => is_string($context->data->getItem()->getRuleName())
                ? substr($context->data->getItem()->getRuleName(), 30, -4)
                : $translator->translate(id: 'message.no-rule')
            ,
            valueAttributes: static fn (ValueContext $context) => $context->data->getItem()->getRuleName() === null
                ? ['class' => 'no_rule']
                : []
            ,
            fieldAttributes: ['class' => 'rule'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.created-at'),
            value: static fn (GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getItem()->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
            fieldAttributes: ['class' => 'created-at datetime'],
        ),
        new DataField(
            label: $translator->translate(id: 'label.updated-at'),
            value: static fn (GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getItem()->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
            fieldAttributes: ['class' => 'updated-at datetime'],
        ),
    )
    ->prepend(
        Html::div(
            content: $translator->translate(
                'label.permission.name',
                ['name' => $item->getItem()->getName()],
                'rbam'
            ),
            attributes: ['class' => 'header']
        )
        . Html::div(NoEncode::string(
            Html::a(
                content: $translator->translate(id: 'button.update', category: 'rbam'),
                url: $urlGenerator->generate(
                    'rbam.item.update',
                    [
                        'name' => $item->getItem()->getName(),
                        'type' => 'permission',
                    ]
                ),
                attributes: $rbamParameters->getButtons('update')['attributes']
            )
            . Html::a(
                content: $translator->translate(id: 'button.translations', category: 'rbam'),
                url: $urlGenerator->generate(
                    'rbam.item.translate',
                    [
                        'name' => $item->getItem()->getName(),
                        'type' => 'permission',
                    ]
                ),
                attributes: $rbamParameters->getButtons('translate')['attributes']
            ))
        )
    )
    ->render()
;

echo Tabs::widget([
    'tabs' => [
        $translator->translate(id: 'label.diagram', category: 'rbam') => $diagram->render(),
        $translator->translate(id: 'label.child-permissions', category: 'rbam') => $this->render(
            '_items',
            [
                'actionButtons' => ['view'],
                'currentUser' => $currentUser,
                'noResultsText' => 'message.permission.none-found',
                'header' => '',
                'item' => $item,
                'items' => $children,
                'paginationUrl' => $urlGenerator->generate(
                    'rbam.item.child-items',
                    [
                        'name' => $item->getItem()->getName(),
                        'type' => Item::TYPE_PERMISSION,
                    ]
                ),
                'toolbar' => Html::div(Html::a(
                    content: $translator->translate(
                        id: $rbamParameters->getButtons('manageChildPermissions')['content'],
                        category: 'rbam'
                    ),
                    url: $urlGenerator->generate(
                        'rbam.item.manage-children',
                        [
                            'childType' => Item::TYPE_PERMISSION,
                            'name' => $item->getItem()->getName(),
                            'type' => Item::TYPE_PERMISSION,
                        ]
                    ),
                    attributes: $rbamParameters->getButtons('manageChildRoles')['attributes']
                )),
                'translator' => $translator,
                'type' => Item::TYPE_PERMISSION,
                'urlGenerator' => $urlGenerator,
                'user' => null,
            ]
        ),
        $translator->translate(id: 'label.users.permitted') => $this->render(
            '_permittedUsers',
            [
                'permission' => $item->getItem(),
                'users' => $users,
            ]
        ),
    ]
]);