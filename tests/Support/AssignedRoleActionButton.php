<?php

namespace Tests\Support;

enum AssignedRoleActionButton: int implements ActionButtonInterface
{
    case revoke = 1;
}