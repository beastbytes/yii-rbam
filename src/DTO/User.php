<?php

namespace BeastBytes\Yii\Rbam\DTO;


use BeastBytes\Yii\Rbam\User\UserInterface;
use YiiSoft\Rbac\Assignment;
use Yiisoft\Rbac\Role;

final class User
{
    private ?Assignment $assignment = null;
    private int $permissionCount = 0;
    private int $roleCount = 0;
    private ?Role $role = null;

    public function __construct(private UserInterface $user)
    {
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function getPermissionCount(): int
    {
        return $this->permissionCount;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function getRoleCount(): int
    {
        return $this->roleCount;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function withAssignment(Assignment $assignment): self
    {
        $new = clone $this;
        $new->assignment = $assignment;
        return $new;
    }

    public function withPermissionCount(int $count): self
    {
        $new = clone $this;
        $new->permissionCount = $count;
        return $new;
    }

    public function withRole(Role $role): self
    {
        $new = clone $this;
        $new->role = $role;
        return $new;
    }

    public function withRoleCount(int $count): self
    {
        $new = clone $this;
        $new->roleCount = $count;
        return $new;
    }
}