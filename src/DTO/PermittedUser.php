<?php

namespace BeastBytes\Yii\Rbam\DTO;


use BeastBytes\Yii\Rbam\User\UserInterface;
use YiiSoft\Rbac\Assignment;
use Yiisoft\Rbac\Role;

final class PermittedUser
{
    public function __construct(
        private readonly UserInterface $user,
        private readonly Role $role,
        private readonly Assignment $assignment,
    )
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

    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }
}