<?php

namespace Tests\Support;

use Yiisoft\Rbac\AssignmentsStorageInterface;

enum ChildActionButton: int implements ActionButtonInterface
{
    case remove = 1;
}