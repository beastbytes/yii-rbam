<?php

namespace BeastBytes\Yii\Rbam;

interface InitialisationServiceInterface
{
    public function getErrors(): array;

    public function hasErrors(): bool;

    public function processFile(string $file): void;
}