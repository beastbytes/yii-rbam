<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var RbamItem[] $children
 * @var int $currentPage
 * @var CurrentUser $currentUser
 * @var MermaidHierarchyDiagram $diagram
 * @var RbamItem $item
 * @var PermittedUser[] $permittedUsers
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Mermaid\Mermaid;
use BeastBytes\Yii\Rbam\Alpine\Tabs;
use BeastBytes\Yii\Rbam\Diagram\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\DTO\Item as RbamItem;
use BeastBytes\Yii\Rbam\DTO\PermittedUser;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
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
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.permissions'),
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
            label: $translator->translate('label.name'),
            value: static fn(GetValueContext $context) => $translator->translate($context->data->getItem()->getName()),
        ),
        new DataField(
            label: $translator->translate('label.type'),
            value: $translator->translate('label.permission'),
        ),
        new DataField(
            label: $translator->translate('label.description'),
            value: static fn(GetValueContext $context)
                => $translator->translate($context->data->getItem()->getDescription())
            ,
        ),
        new DataField(
            label: $translator->translate('label.granted-by'),
            value: static function (GetValueContext $context) use ($translator) {
                $grantedBy = [];

                foreach ($context->data->getParents() as $parent) {
                    $grantedBy[] = $translator->translate($parent->getName());
                }

                return '<div>' . implode('</div><div>', $grantedBy) . '</div>';
            },
            valueEncode: false,
        ),
        new DataField(
            label: $translator->translate('label.rule'),
            value: static fn(GetValueContext $context) => is_string($context->data->getItem()->getRuleName())
                ? substr($context->data->getItem()->getRuleName(), 30, -4)
                : $translator->translate('message.no-rule')
            ,
            valueAttributes: static fn(ValueContext $context) => $context->data->getItem()->getRuleName() === null
                ? ['class' => 'no_rule']
                : []
        ),
        new DataField(
            label: $translator->translate('label.created-at'),
            value: static fn(GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getItem()->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
        new DataField(
            label: $translator->translate('label.updated-at'),
            value: static fn(GetValueContext $context) => (new DateTime())
                ->setTimestamp($context->data->getItem()->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
    )
    ->prepend(
        Html::div(
            content: $translator->translate('label.permission.name', ['name' => $item->getItem()->getName()]),
            attributes: ['class' => 'header']
        )
        . Html::div(Html::a(
            content: $translator->translate('button.update'),
            url: $urlGenerator->generate(
                'rbam.item.update',
                [
                    'name' => $item->getItem()->getName(),
                    'type' => 'permission',
                ]
            ),
            attributes: $rbamParameters->getButtons('update')['attributes']
        ))
    )
    ->render()
;

echo Tabs::widget([
    'tabs' => [
        $translator->translate('label.diagram') => $diagram->render(),
        $translator->translate('label.child-permissions') => $this->render(
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
                        $rbamParameters->getButtons('manageChildPermissions')['content']
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
        $translator->translate('label.users.permitted') => $this->render(
            '_permittedUsers',
            [
                'permission' => $item->getItem(),
                'permittedUsers' => $permittedUsers,
            ]
        ),
    ]
]);