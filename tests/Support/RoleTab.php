<?php

namespace Tests\Support;

use Tests\Support\TabInterface;

enum RoleTab: int implements TabInterface
{
    case diagram = 1;
    case childRoles = 2;
    case permissions = 3;
    case assignments = 4;
}
