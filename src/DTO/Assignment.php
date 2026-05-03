<?php

namespace BeastBytes\Yii\Rbam\DTO;

use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Rbac\Role;

final class Assignment
{
    /**
     * @param UserInterface $user The assigned user
     * @param Role $role The Role providing the assignment
     */
    public function __construct(private readonly UserInterface $user, private readonly Role $role)
    {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}