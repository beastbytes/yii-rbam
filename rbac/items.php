<?php

return [
    [
        'name' => 'RbacItemCreate',
        'description' => 'Create Permissions and Roles',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacItemRemove',
        'description' => 'Remove Permissions and Roles',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacItemUpdate',
        'description' => 'Update Permissions and Roles',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacItemView',
        'description' => 'View Permissions and Roles',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbamIndex',
        'description' => 'View RBAM',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacRuleCreate',
        'description' => 'Create Rules',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacRuleDelete',
        'description' => 'Delete Rules',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacRuleUpdate',
        'description' => 'Update Rules',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacRuleView',
        'description' => 'View Rules',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacUserUpdate',
        'description' => 'Update Users',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbacUserView',
        'description' => 'View Users',
        'type' => 'permission',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
    ],
    [
        'name' => 'RbamItemsManager',
        'description' => 'Can view, create, update, and delete RBAC Roles and Permissions',
        'type' => 'role',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
        'children' => [
            'RbamIndex',
            'RbacItemCreate',
            'RbacItemRemove',
            'RbacItemUpdate',
            'RbacItemView',
        ],
    ],
    [
        'name' => 'RbamRulesManager',
        'description' => 'Can view, create, update, and delete RBAC Rules',
        'type' => 'role',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
        'children' => [
            'RbamIndex',
            'RbacRuleCreate',
            'RbacRuleDelete',
            'RbacRuleUpdate',
            'RbacRuleView',
        ],
    ],
    [
        'name' => 'RbamUsersManager',
        'description' => 'Can view users, and assign and revoke RBAC Roles',
        'type' => 'role',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
        'children' => [
            'RbamIndex',
            'RbacUserUpdate',
            'RbacUserView',
        ],
    ],
    [
        'name' => 'Rbam',
        'description' => 'Can perform all RBAM functions',
        'type' => 'role',
        'updated_at' => 1741612689,
        'created_at' => 1741612689,
        'children' => [
            'RbamItemsManager',
            'RbamRulesManager',
            'RbamUsersManager',
        ],
    ],
];
