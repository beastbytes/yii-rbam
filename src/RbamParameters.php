<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
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

    public function getActionButton(string $name): array
    {
        return $this->parameters['actionButtons'][$name];
    }

    public function getDatetimeFormat(): string
    {
        return $this->parameters['datetimeFormat'];
    }

    public function getDefaultRoles(): array
    {
        return $this->parameters['defaultRoles'];
    }
}
