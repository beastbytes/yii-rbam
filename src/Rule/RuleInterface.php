<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

interface RuleInterface
{
    public function getCode(): string;
    public function getDescription(): string;
    public function getName(): string;
}