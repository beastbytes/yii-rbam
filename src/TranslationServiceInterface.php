<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Rule\RuleInterface;
use Yiisoft\Rbac\Item;

interface TranslationServiceInterface
{
    public const string TYPE_ITEM = 'item';
    public const string TYPE_RULE = 'rule';

    public function deleteItem(Item $item): void;
    public function deleteRule(string $name): void;
    public function getItemTranslations(Item $item): array;
    public function getRuleTranslations(RuleInterface $rule): array;
    public function save(array $translations): void;
    public function updateItem(Item $oldItem, Item $newItem): void;
    public function updateRule(string $oldDescription, string $newDescription): void;
}