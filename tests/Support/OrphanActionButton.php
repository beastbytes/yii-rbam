<?php

namespace Tests\Support;

use Yiisoft\Rbac\AssignmentsStorageInterface;

enum OrphanActionButton: int implements ActionButtonInterface
{
    case add = 1;
}