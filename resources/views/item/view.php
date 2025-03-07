<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Role[] $ancestors
 * @var AssetManager $assetManager
 * @var Assignment[] $assignments
 * @var AssignmentsStorageInterface $assignmentsStorage
 * @var Inflector $inflector
 * @var Item $item
 * @var ItemsStorageInterface $itemsStorage
 * @var Permission[] $permissions
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Mermaid\ClassDiagram\Attribute;
use BeastBytes\Mermaid\ClassDiagram\ClassDiagram;
use BeastBytes\Mermaid\ClassDiagram\Classs;
use BeastBytes\Mermaid\ClassDiagram\Method;
use BeastBytes\Mermaid\ClassDiagram\Relationship;
use BeastBytes\Mermaid\ClassDiagram\RelationshipType;
use BeastBytes\Mermaid\InteractionType;
use BeastBytes\Mermaid\Mermaid;
use BeastBytes\Yii\Rbam\ItemTypeService;
use BeastBytes\Yii\Rbam\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Script;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;

$this->registerScriptTag(
    Script::tag()
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
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.' . ItemTypeService::getItemType($item) . 's'),
        'url' => $urlGenerator->generate(
            'rbam.itemIndex',
            [
                'type' => ItemTypeService::getItemType($item) . 's'
            ]
        ),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= DetailView::widget()
    ->attributes(['class' => 'detail-view ' . ItemTypeService::getItemType($item)])
    ->fieldTemplate('{label}{value}')
    ->data($item)
    ->fields(
        new DataField(
            label: $translator->translate('label.name'),
            value: fn($item) => $item->getName(),
        ),
        new DataField(
            label: $translator->translate('label.type'),
            value: static function($item) use ($translator)
            {
                return $translator->translate('label.' . ItemTypeService::getItemType($item));
            },
        ),
        new DataField(
            label: $translator->translate('label.description'),
            value: fn($item) => $item->getDescription(),
        ),
        new DataField(
            label: $translator->translate('label.rule'),
            value: fn($item) => $item->getRuleName() ?? $translator->translate('message.no-rule'),
            valueAttributes: fn($item) => $item->getRuleName() === null ? ['class' => 'no_rule'] : []
        ),
        new DataField(
            label: $translator->translate('label.created-at'),
            value: fn($item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
        new DataField(
            label: $translator->translate('label.updated-at'),
            value: fn($item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
            ,
        ),
    )
    ->header(
        Html::div(
            $translator->translate(
                'label.' . ItemTypeService::getItemType($item) . '-name',
                ['name' => $item->getName()]
            ),
            ['class' => 'header']
        )
        . Html::a(
            $translator->translate('button.update'),
            $urlGenerator->generate('rbam.updateItem', [
                'name' => $inflector->toSnakeCase($item->getName()),
                'type' => ItemTypeService::getItemType($item),
            ]),
            ['class' => 'btn btn_update']
        )
            ->render()
    )
    ->render()
?>

<?= $this->render(
    '_' . ItemTypeService::getItemType($item),
    [
        'ancestors' => $ancestors,
        'assignments' => $assignments,
        'assignmentsStorage' => $assignmentsStorage,
        'diagram' => (new MermaidHierarchyDiagram($item, $itemsStorage, $inflector, $translator, $urlGenerator)),
        'item' => $item,
        'itemsStorage' => $itemsStorage,
        'permissions' => $permissions,
        'rbamParameters' => $rbamParameters,
        'roles' => $roles,
        'users' => $users,
    ]
) ?>