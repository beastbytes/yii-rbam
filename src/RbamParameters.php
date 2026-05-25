<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

final readonly class RbamParameters
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

    public function getGuestRole(): array
    {
        return $this->parameters['guestRole'];
    }

    public function getMermaidDiagramStyles(): array
    {
        return $this->parameters['mermaidDiagramStyles'];
    }

    public function getPageSize(): int
    {
        return $this->parameters['pageSize'];
    }

    public function getTabPageSize(): int
    {
        return $this->parameters['tabPageSize'];
    }
}