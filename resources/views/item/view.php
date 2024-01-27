<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Inflector $inflector
 * @var Item $item
 * @var ItemsStorageInterface $itemStorage
 * @var array $permissions
 * @var RbamParameters $rbamParameters
 * @var array $roles
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var array $users
 */

use BeastBytes\Mermaid\ClassDiagram\Attribute;
use BeastBytes\Mermaid\ClassDiagram\ClassDiagram;
use BeastBytes\Mermaid\ClassDiagram\Classs;
use BeastBytes\Mermaid\ClassDiagram\Method;
use BeastBytes\Mermaid\ClassDiagram\Relationship;
use BeastBytes\Mermaid\ClassDiagram\RelationshipType;
use BeastBytes\Mermaid\InteractionType;
use BeastBytes\Mermaid\Mermaid;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Script;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
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

$this->setTitle(
    $item->getName() . ' ' . ucfirst($item->getType())
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.' . $item->getType() . 's'),
        'url' => $urlGenerator->generate('rbam.itemIndex', ['type' => $item->getType() . 's']),
    ],
    Html::encode($this->getTitle()),
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= DetailView::widget()
    ->attributes(['class' => 'detail_view ' . $item->getType()])
    ->data($item)
    ->fields(
        DataField::create()
            ->label($translator->translate('label.name'))
            ->value(fn($item) => $item->getName())
            ->valueTag('span'),
        DataField::create()
            ->label($translator->translate('label.type'))
            ->value(fn($item) => ucfirst($item->getType()))
            ->valueTag('span'),
        DataField::create()
            ->label($translator->translate('label.description'))
            ->value(fn($item) => $item->getDescription())
            ->valueTag('span'),
        DataField::create()
            ->label($translator->translate('label.rule_name'))
            ->value(fn($item) => $item->getRuleName() ?? $translator->translate('message.no_rule'))
            ->valueTag('span')
            ->valueAttributes(fn($item) => $item->getRuleName() === null ? ['class' => 'no_rule'] : []),
        DataField::create()
            ->label($translator->translate('label.created_at'))
            ->value(fn($item) => (new DateTime())->setTimestamp($item->getCreatedAt())->format($rbamParameters->getDatetimeFormat()))
            ->valueTag('span'),
        DataField::create()
            ->label($translator->translate('label.updated_at'))
            ->value(fn($item) => (new DateTime())->setTimestamp($item->getUpdatedAt())->format($rbamParameters->getDatetimeFormat()))
            ->valueTag('span'),
    )
    ->header(
        Html::a(
            $translator->translate('button.update_' . $item->getType(), [
                'name' => $item->getName(),
            ]),
            $urlGenerator->generate('rbam.updateItem', [
                'name' => $inflector->toSnakeCase($item->getName()),
                'type' => $item->getType(),
            ])
        )
        ->render()
    )
    ->render()
?>

<?= $this->render(
    '_' . $item->getType(),
    [
        'item' => $item,
        'permissions' => $permissions,
        'roles' => $roles,
        'users' => $users,
    ]
) ?>

<?php
// Generate Mermaid.js ClassDiagram
$classes = [];
$relationships = [];

$currentItem = (new Classs($item->getName(), ucfirst($item->getType())))
    ->withMember(new Attribute($item->getDescription()))
    ->withStyleClass('current_' . $item->getType())
;
$classes[] = $item->getRuleName() ? $currentItem->addMember(new Method($item->getRuleName())) : $currentItem;

$child = $currentItem;
foreach ($itemStorage->getParents($item->getName()) as $ancestor):
    $parent = (new Classs($ancestor->getName(), ucfirst($ancestor->getType())))
        ->withMember(new Attribute($ancestor->getDescription()))
        ->withStyleClass('ancestor_' . $ancestor->getType())
        ->withInteraction(
            $urlGenerator->generate(
                'rbam.viewItem',
                ['type' => $ancestor->getType(), 'name' => $inflector->toSnakeCase($ancestor->getName())]
            ),
            InteractionType::Link
        )
    ;
    $classes[] = $ancestor->getRuleName() ? $parent->addMember(new Method($ancestor->getRuleName())) : $parent;
    $relationships[] = new Relationship($parent, $child, RelationshipType::Inheritance);
    $child = $parent;
endforeach;

getDescendants($currentItem, $itemStorage, $classes, $relationships, $inflector, $urlGenerator);

echo (new ClassDiagram())
    ->withClass(...$classes)
    ->withRelationship(...$relationships)
    ->render(['id' => 'mermaid'])
;

function getDescendants(
    Classs $item,
    ItemsStorageInterface $itemStorage,
    &$classes,
    &$relationships,
    $inflector,
    $urlGenerator
): void
{
    foreach ($itemStorage->getDirectChildren($item->getId()) as $child):
        $childClass = (new Classs($child->getName(), ucfirst($child->getType())))
            ->withMember(new Attribute($child->getDescription()))
            ->withStyleClass('descendant_' . $child->getType())
            ->withInteraction(
                $urlGenerator->generate(
                    'rbam.viewItem',
                    ['type' => $child->getType(), 'name' => $inflector->toSnakeCase($child->getName())]
                ),
                InteractionType::Link
            )
        ;
        $classes[] = $child->getRuleName() ? $childClass->addMember(new Method($child->getRuleName())) : $childClass;
        $relationships[] = new Relationship($item, $childClass, RelationshipType::Inheritance);
        getDescendants($childClass, $itemStorage, $classes, $relationships, $inflector, $urlGenerator);
    endforeach;
}
