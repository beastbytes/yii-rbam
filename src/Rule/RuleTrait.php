<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use phpDocumentor\Reflection\Types\This;
use ReflectionClass;

trait RuleTrait
{
    public function getCode(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $executeMethod = $reflectionClass->getMethod('execute');

        $offset = $executeMethod->getStartLine() + 1;
        $length = $executeMethod->getEndLine() - 1 - $offset;

        $lines = array_slice(file($reflectionClass->getFileName()), $offset, $length);

        array_walk($lines, fn(string &$line) => $line = substr($line, 4, -1));

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