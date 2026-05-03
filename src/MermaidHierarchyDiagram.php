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
use BeastBytes\Mermaid\Mermaid;
use RuntimeException;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;

final class MermaidHierarchyDiagram implements HierarchyDiagramInterface
{
    private array $classes = [];
    private ?Item $item = null;
    private array $relationships = [];

    public function __construct(
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function withItem(Item $item): self
    {
        $new = clone $this;
        $new->item = $item;
        return $new;
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $currentItem = (new Classs(
            $this->item->getName(),
            $this->translator->translate('label.' . ItemTypeService::getItemType($this->item))
        ))
            ->withAttribute(new Attribute($this->item->getDescription()))
            ->withStyleClass('current_' . ItemTypeService::getItemType($this->item))
        ;
        $this->classes[$this->item->getName()] = is_string($this->item->getRuleName())
            ? $currentItem->withMethod(new Method($this->item->getRuleName()))
            : $currentItem
        ;

        $this->ancestors($currentItem);
        $this->descendants($currentItem);
        $this->relationships();

        $return = Mermaid::create(ClassDiagram::class)
            ->withClass(...array_values($this->classes))
            ->withRelationship(...$this->relationships)
            ->render(['id' => 'mermaid'])
        ;

        return str_replace('<<', '&lt;&lt;', $return);
    }

    private function ancestors(Classs $child): void
    {
        $childName = $child->getId();
        $parentItems = $this->itemsStorage->getParents($child->getId());

        foreach ($parentItems as $parentItem) {
            $parent = (new Classs(
                $parentItem->getName(),
                $this->translator->translate('label.' . $parentItem->getType())
            ))
                ->withAttribute(new Attribute($parentItem->getDescription()))
                ->withStyleClass('ancestor_' . $parentItem->getType())
                ->withInteraction(
                    $this->urlGenerator->generate(
                        'rbam.item.view',
                        [
                            'type' => $parentItem->getType(),
                            'name' => $parentItem->getName()
                        ]
                    ),
                    InteractionType::link
                )
            ;

            if (!array_key_exists($parentItem->getName(), $this->classes)) {
                $this->classes[$parentItem->getName()] = $parentItem->getRuleName()
                    ? $parent->addMethod(new Method($parentItem->getRuleName()))
                    : $parent
                ;
            }
        }
    }

    private function descendants(Classs $parent): void
    {
        foreach ($this->itemsStorage->getDirectChildren($parent->getId()) as $item) {
            $child = (new Classs(
                $item->getName(),
                $this->translator->translate('label.' . ItemTypeService::getItemType($item))
            ))
                ->withAttribute(new Attribute($item->getDescription()))
                ->withStyleClass('descendant_' . ItemTypeService::getItemType($item))
                ->withInteraction(
                    $this->urlGenerator->generate(
                        'rbam.item.view',
                        [
                            'type' => ItemTypeService::getItemType($item),
                            'name' => $item->getName()
                        ]
                    ),
                    InteractionType::link
                )
            ;

            if (!array_key_exists($item->getName(), $this->classes)) {
                $this->classes[$item->getName()] = is_string($item->getRuleName())
                    ? $child->addMethod(new Method($item->getRuleName()))
                    : $child
                ;
            }

            $this->descendants($child);
        }
    }

    private function relationships(): void
    {
        foreach ($this->classes as $parent) {
            foreach ($this->classes as $child) {
                if ($this->itemsStorage->hasDirectChild($parent->getId(), $child->getId())) {
                    $this->relationships[] = new Relationship($parent, $child, RelationshipType::inheritance);
                }
            }
        }
    }
}