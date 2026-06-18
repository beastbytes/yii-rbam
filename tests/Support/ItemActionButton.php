<?php

namespace Tests\Support;

use Yiisoft\Rbac\AssignmentsStorageInterface;

enum ItemActionButton: int implements ActionButtonInterface
{
    case view = 1;
    case update = 2;
    case remove = 3;
}