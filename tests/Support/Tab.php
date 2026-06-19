<?php

namespace Tests\Support;

enum Tab: string
{
    case assignments = 'tab_assignments';
    case diagram = 'tab_diagram';
    case childPermissions = 'tab_child_permissions';
    case childRoles = 'tab_child_roles';
    case permissions = 'tab_permissions';
    case permittedUsers = 'tab_permitted_users';
}