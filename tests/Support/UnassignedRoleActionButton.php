<?php

namespace Tests\Support;

enum UnassignedRoleActionButton: int implements ActionButtonInterface
{
    case assign = 1;
}