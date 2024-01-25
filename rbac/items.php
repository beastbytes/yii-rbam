<?php

return [
    'Admin' => [
        'name' => 'Admin',
        'description' => 'God mode',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'Child',
        ],
    ],
    'Child' => [
        'name' => 'Child',
        'description' => 'A child rule',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'PostManager',
            'UserManager',
        ],
    ],
    'PostManager' => [
        'name' => 'PostManager',
        'description' => 'Post Manager',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'DeletePost',
            'UpdatePost',
        ],
    ],
    'UserManager' => [
        'name' => 'UserManager',
        'description' => 'User Manager',
        'rule_name' => 'True',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'CreateUser',
            'DeleteUser',
            'UpdateUser',
        ],
    ],
    'CreateUser' => [
        'name' => 'CreateUser',
        'description' => 'Create a User',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    'UpdateUser' => [
        'name' => 'UpdateUser',
        'description' => 'Update a user',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    'DeleteUser' => [
        'name' => 'DeleteUser',
        'description' => 'Delete a user',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    'UpdatePost' => [
        'name' => 'UpdatePost',
        'description' => 'Update a post',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    'DeletePost' => [
        'name' => 'DeletePost',
        'description' => 'Delete a post',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
];
