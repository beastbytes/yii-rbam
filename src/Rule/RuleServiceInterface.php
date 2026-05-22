<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

Interface RuleServiceInterface
{
    public function delete(string $name): void;

    public function getRule(string $name): ?RuleInterface;

    /** @return array<string, RuleInterface> */
    public function getRules(): array;

    /** @return array<string, string> */
    public function getRuleClasses(): array;

    public function save(string $name, string $description, string $code): bool;
}