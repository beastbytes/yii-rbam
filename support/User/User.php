<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Support\User;

use BeastBytes\Yii\Rbam\User\UserInterface;

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
