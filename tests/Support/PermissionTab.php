<?php

namespace Tests\Support;

enum PermissionTab: int implements TabInterface
{
    case diagram = 1;
    case childPermissions = 2;
    case permittedUsers = 3;
}