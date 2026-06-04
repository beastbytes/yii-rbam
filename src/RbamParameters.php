<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

final readonly class RbamParameters
{
    public function __construct(private array $parameters)
    {
    }

    public function getApplicationLayout()
    {
        return $this->parameters['applicationLayout'];
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

    public function getDiagramStyles(): array
    {
        return $this->parameters['diagramStyles'];
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