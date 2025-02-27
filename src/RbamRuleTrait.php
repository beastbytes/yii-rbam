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

        foreach ($lines as &$line) {
            $line = str_repeat(' ', 4) . mb_trim($line);
        }

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