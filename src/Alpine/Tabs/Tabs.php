<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Alpine\Tabs;

use BeastBytes\Yii\Rbam\Alpine\AlpineAsset;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Widget\Widget;

final class Tabs extends Widget
{
    /** @var string $containerClass CSS class for the container tag */
    private string $containerClass = 'alpine-tabs';
    /** @var string $panelClass CSS class for tab content panels */
    private string $panelClass = 'panel';
    /** @var string $panelContainerClass CSS class for tab content panels container */
    private string $panelContainerClass = 'panels';
    /** @var string $selectedTabClass CSS class for the selected tab */
    private string $selectedTabClass = 'selected';
    /** @var string $tabClass CSS class for tabs */
    private string $tabClass = 'tab';
    /** @var string $tabContainerClass CSS class for the tabs container */
    private string $tabContainerClass = 'tabs';
    private array $tabs = [];
    /** @var string $unselectedTabClass CSS class for unselected tabs */
    private string $unselectedTabClass = 'unselected';

    public function __construct(AssetManager $assetManager)
    {
        $assetManager->register(TabsAsset::class);
    }

    /**
     * @param array{tab: string, panel: string} ...$tabs
     * @return self
     */
    public function addTabs(array ...$tabs): self
    {
        $new = clone $this;
        $new->tabs = array_merge($this->tabs, $tabs);
        return $new;
    }

    public function containerClass(string $containerClass): self
    {
        $new = clone $this;
        $new->containerClass = $containerClass;
        return $new;
    }

    public function tabClass(string $tabClass): self
    {
        $new = clone $this;
        $new->tabClass = $tabClass;
        return $new;
    }

    public function tabContainerClass(string $tabContainerClass): self
    {
        $new = clone $this;
        $new->tabContainerClass = $tabContainerClass;
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

    public function selectedTabClass(string $selectedTabClass): self
    {
        $new = clone $this;
        $new->selectedTabClass = $selectedTabClass;
        return $new;
    }


    /**
     * @param array{tab: string, panel: string} ...$tabs
     * @return self
     */
    public function tabs(array ...$tabs): self
    {
        $new = clone $this;
        $new->tabs = $tabs;
        return $new;
    }

    public function unselectedTabClass(string $unselectedTabClass): self
    {
        $new = clone $this;
        $new->unselectedTabClass = $unselectedTabClass;
        return $new;
    }

    public function render(): string
    {
        return sprintf(
            '<div x-data x-tabs class="%s"><div x-tabs:list class="%s">%s</div><div x-tabs:panels class="%s">%s</div></div>',
            $this->containerClass,
            $this->tabContainerClass,
            $this->renderTabs(),
            $this->panelContainerClass,
            $this->renderPanels()
        );
    }

    private function renderTabs(): string
    {
        $tabs = [];

        foreach ($this->tabs as $tab) {
            $tabs[] = sprintf(
                '<button x-tabs:tab type="button" :class="$tab.isSelected ? \'%s\' : \'%s\'" class="%s">%s</button>',
                $this->selectedTabClass,
                $this->unselectedTabClass,
                $this->tabClass,
                $tab['tab']
            );
        }

        return implode('', $tabs);
    }

    private function renderPanels(): string
    {
        $panels = [];

        foreach ($this->tabs as $tab) {
            $panels[] = sprintf(
                '<section x-tabs:panel class="%s">%s</section>',
                $this->panelClass,
                $tab['panel']
            );
        }

        return implode('', $panels);
    }
}