<?php

namespace Tests\Support;

use Tests\Support\TabInterface;

enum PermissionTab: int implements TabInterface
{
    case diagram = 1;
    case childPermissions = 2;
    case permittedUsers = 3;
}