<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Mermaid\ClassDiagram\Attribute;
use BeastBytes\Mermaid\ClassDiagram\ClassDiagram;
use BeastBytes\Mermaid\ClassDiagram\Classs;
use BeastBytes\Mermaid\ClassDiagram\Method;
use BeastBytes\Mermaid\ClassDiagram\Relationship;
use BeastBytes\Mermaid\ClassDiagram\RelationshipType;
use BeastBytes\Mermaid\InteractionType;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;

final class MermaidHierarchyDiagram implements HierarchyDiagramInterface
{
    private array $classes = [];
    private array $relationships = [];

    public function __construct(
        private readonly Item $item,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly Inflector $inflector,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $ancestors = $this
            ->itemsStorage
            ->getParents($this->item->getName())
        ;

        $currentItem = (new Classs(
            $this->item->getName(),
            $this->translator->translate('label.' . $this->item->getType())
        ))
            ->withMember(new Attribute($this->item->getDescription()))
            ->withStyleClass('current_' . $this->item->getType())
        ;
        $this->classes[] = $this->item->getRuleName()
            ? $currentItem->addMember(new Method($this->item->getRuleName()))
            : $currentItem
        ;

        $child = $currentItem;

        foreach ($ancestors as $ancestor) {
            $parent = (new Classs(
                $ancestor->getName(),
                $this->translator->translate('label.' . $ancestor->getType())
            ))
                ->withMember(new Attribute($ancestor->getDescription()))
                ->withStyleClass('ancestor_' . $ancestor->getType())
                ->withInteraction(
                    $this->urlGenerator->generate(
                        'rbam.viewItem',
                        [
                            'type' => $ancestor->getType(),
                            'name' => $this->inflector->toSnakeCase($ancestor->getName())
                        ]
                    ),
                    InteractionType::Link
                )
            ;
            $this->classes[] = $ancestor->getRuleName() ? $parent->addMember(new Method($ancestor->getRuleName())) : $parent;
            $this->relationships[] = new Relationship($parent, $child, RelationshipType::Inheritance);
            $child = $parent;
        }

        $this->getDescendants($currentItem);

        return (new ClassDiagram())
            ->withClass(...$this->classes)
            ->withRelationship(...$this->relationships)
            ->render(['id' => 'mermaid'])
        ;
    }

    function getDescendants(Classs $item): void
    {
        foreach ($this->itemsStorage->getDirectChildren($item->getId()) as $child) {
            $childClass = (new Classs(
                $child->getName(),
                $this->translator->translate('label.' . $child->getType())
            ))
                ->withMember(new Attribute($child->getDescription()))
                ->withStyleClass('descendant_' . $child->getType())
                ->withInteraction(
                    $this->urlGenerator->generate(
                        'rbam.viewItem',
                        [
                            'type' => $child->getType(),
                            'name' => $this->inflector->toSnakeCase($child->getName())
                        ]
                    ),
                    InteractionType::Link
                )
            ;
            $this->classes[] = $child->getRuleName()
                ? $childClass->addMember(new Method($child->getRuleName()))
                : $childClass;
            $this->relationships[] = new Relationship($item, $childClass, RelationshipType::Inheritance);
            $this->getDescendants($childClass);
        }
    }
}