<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\User;

use BeastBytes\Yii\Rbam\UserInterface;

class User implements UserInterface
{
    public function __construct(private readonly string $id, private readonly string $name)
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
