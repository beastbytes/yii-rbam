<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use ReflectionClass;

use const DIRECTORY_SEPARATOR;

trait RbamRuleTrait
{    public function getCode(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $executeMethod = $reflectionClass->getMethod('execute');

        $offset = $executeMethod->getStartLine() + 1;
        $length = $executeMethod->getEndLine() - 1 - $offset;

        $lines = array_slice(file($reflectionClass->getFileName()), $offset, $length);
        
        array_unshift(
            $lines,
            'public function execute(?string $userId, Item $item, RuleContext $context): bool',
            '{'
        );

        array_push($lines, '}');

        return implode("\n", $lines);
    }
    
    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }
    
    public function getName(): string
    {
        $reflectionClass = new ReflectionClass($this);
        return substr($reflectionClass->getShortName(), 0, -4);
    }
}