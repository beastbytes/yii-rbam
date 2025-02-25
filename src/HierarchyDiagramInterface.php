<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;

Interface HierarchyDiagramInterface extends \Stringable
{
    public function __construct(
        Item $item,
        ItemsStorageInterface $itemsStorage,
        Inflector $inflector,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    );
    public function render(): string;
}