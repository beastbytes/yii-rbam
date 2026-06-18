<?php

namespace Tests\Support;

use Yiisoft\Rbac\AssignmentsStorageInterface;

enum AssignedRoleActionButton: int implements ActionButtonInterface
{
    case revoke = 1;
}