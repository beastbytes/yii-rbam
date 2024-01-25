<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use BeastBytes\Yii\Rbam\RuleInterface;
use ReflectionClass;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;

use const DIRECTORY_SEPARATOR;

final class FalseRule implements RuleInterface
{
    private string $description = 'Always returns FALSE';
    
    public function execute(?string $userId, Item $item, RuleContext $ruleContext): bool
    {
        return false;
    }
    
    public function getCode(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $executeMethod = $reflectionClass->getMethod('execute');

        $offset = $executeMethod->getStartLine() + 1;
        $length = $executeMethod->getEndLine() - 1 - $offset;

        $filename = __DIR__ . DIRECTORY_SEPARATOR . $reflectionClass->getShortName() . '.php';
        $lines = array_slice(file($filename), $offset, $length);

        foreach ($lines as &$line) {
            $line = trim($line);
        }

        return implode("\n", $lines);
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function getName(): string
    {
        $reflectionClass = new ReflectionClass($this);
        return substr($reflectionClass->getShortName(), 0, -4);
    }
}