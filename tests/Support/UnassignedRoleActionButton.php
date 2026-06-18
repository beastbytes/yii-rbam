<?php

namespace Tests\Support;

use Yiisoft\Rbac\AssignmentsStorageInterface;

enum UnassignedRoleActionButton: int implements ActionButtonInterface
{
    case assign = 1;
}