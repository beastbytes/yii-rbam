<?php

namespace BeastBytes\Yii\Rbam\Alpine\Tabs;

use BeastBytes\Yii\Rbam\Alpine\AlpineComponentAsset;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Widget\Widget;

final class Tabs extends Widget
{
    /** @var string $containerClass CSS class for the container tag */
    private string $containerClass = 'tabs';
    /** @var string $headerClass CSS class for tab headers */
    private string $headerClass = 'header';
    /** @var string $headerContainerClass CSS class for tab headers container */
    private string $headerContainerClass = 'headers';
    /** @var string $panelClass CSS class for tab content panels */
    private string $panelClass = 'panel';
    /** @var string $panelContainerClass CSS class fortab content panels container */
    private string $panelContainerClass = 'panels';
    /** @var string $selectedClass CSS class for the selected tab header */
    private string $selectedClass = 'selected';
    private array $tabs = [];
    /** @var string $unselectedClass CSS class for unselected tab headers */
    private string $unselectedClass = 'unselected';

    public function __construct(AssetManager $assetManager)
    {
        $assetManager->register(AlpineComponentAsset::class);
    }

    public function containerClass(string $containerClass): self
    {
        $new = clone $this;
        $new->containerClass = $containerClass;
        return $new;
    }

    public function headerClass(string $headerClass): self
    {
        $new = clone $this;
        $new->headerClass = $headerClass;
        return $new;
    }

    public function headerContainerClass(string $headerContainerClass): self
    {
        $new = clone $this;
        $new->headerContainerClass = $headerContainerClass;
        return $new;
    }

    public function panelClass(string $panelClass): self
    {
        $new = clone $this;
        $new->panelClass = $panelClass;
        return $new;
    }

    public function panelContainerClass(string $panelContainerClass): self
    {
        $new = clone $this;
        $new->panelContainerClass = $panelContainerClass;
        return $new;
    }

    public function selectedClass(string $selectedClass): self
    {
        $new = clone $this;
        $new->selectedClass = $selectedClass;
        return $new;
    }

    public function tabs(array $tabs): self
    {
        $new = clone $this;
        $new->tabs = $tabs;
        return $new;
    }

    public function unselectedClass(string $unselectedClass): self
    {
        $new = clone $this;
        $new->unselectedClass = $unselectedClass;
        return $new;
    }

    public function render(): string
    {
        return sprintf(
            '<div x-data x-tabs class="%s"><div x-tabs:list class="%s">%s</div><div x-tabs:panels class="%s">%s</div></div>',
            $this->containerClass,
            $this->headerContainerClass,
            $this->renderHeaders(),
            $this->panelContainerClass,
            $this->renderPanels()
        );
    }

    private function renderHeaders(): string
    {
        $headers = [];

        foreach ($this->tabs as $tab) {
            $headers[] = sprintf(
                '<button x-tabs:tab type="button" :class="$tab.isSelected ? \'%s\' : \'%s\'" class="%s">%s</button>',
                $this->selectedClass,
                $this->unselectedClass,
                $this->headerClass,
                $tab['header']
            );
        }

        return implode('', $headers);
    }

    private function renderPanels(): string
    {
        $panels = [];

        foreach ($this->tabs as $tab) {
            $panels[] = sprintf(
                '<section x-tabs:panel class="%s">%s</section>',
                $this->panelClass,
                $tab['content']
            );
        }

        return implode('', $panels);
    }
}