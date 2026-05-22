<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Diagram;

use Stringable;
use Yiisoft\Rbac\Item;

Interface HierarchyDiagramInterface extends Stringable
{
    public function render(): string;
    public function withItem(Item $item): self;
}