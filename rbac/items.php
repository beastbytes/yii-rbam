<?php

return [
    [
        'name' => 'Admin',
        'description' => 'God mode',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'Child',
        ],
    ],
    [
        'name' => 'PostManager',
        'description' => 'Post Manager',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'UpdatePost',
            'DeletePost',
        ],
    ],
    [
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
    [
        'name' => 'CreateUser',
        'description' => 'Create a User',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
        'name' => 'UpdateUser',
        'description' => 'Update a user',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
        'name' => 'DeleteUser',
        'description' => 'Delete a user',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
        'name' => 'UpdatePost',
        'description' => 'Update a post',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
        'name' => 'DeletePost',
        'description' => 'Delete a post',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
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
];
