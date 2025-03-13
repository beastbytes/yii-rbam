<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use Closure;
use Stringable;

final class RbamParameters
{
    public function __construct(private array $parameters)
    {
    }

    public function getButtons(string $name): array
    {
        return $this->parameters['buttons'][$name];
    }

    public function getDatetimeFormat(): string
    {
        return $this->parameters['datetimeFormat'];
    }

    public function getDefaultRoles(): array
    {
        return $this->parameters['defaultRoles'];
    }

    public function getMermaidDiagramStyles(): array
    {
        return $this->parameters['mermaidDiagramStyles'];
    }

    public function getPageSize(): array
    {
        return $this->parameters['pageSize'];
    }

    public function getTabPageSize(): array
    {
        return $this->parameters['tabPageSize'];
    }
}