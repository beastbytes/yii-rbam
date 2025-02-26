<?php

return [
    [
        'name' => 'Admin',
        'description' => 'Omnipotent',
        'type' => 'role',
        'updated_at' => 1740494279,
        'created_at' => 1705268964,
        'children' => [
            'PostManager',
            'UserManager',
        ],
    ],
    [
        'name' => 'PostManager',
        'description' => 'Post Manager',
        'type' => 'role',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
        'children' => [
            'CreatePost',
            'UpdatePost',
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
            'UpdateUser',
        ],
    ],
    [
        'name' => 'CreatePost',
        'description' => 'Create a blog post',
        'type' => 'permission',
        'updated_at' => 1705268964,
        'created_at' => 1705268964,
    ],
    [
        'name' => 'UpdatePost',
        'description' => 'Update a blog post',
        'type' => 'permission',
        'updated_at' => 1707533959,
        'created_at' => 1707533959,
    ],
    [
        'name' => 'CreateUser',
        'description' => 'Create a user',
        'type' => 'permission',
        'updated_at' => 1707534071,
        'created_at' => 1707534071,
    ],
    [
        'name' => 'UpdateUser',
        'description' => 'Update a user',
        'type' => 'permission',
        'updated_at' => 1707673497,
        'created_at' => 1707673497,
    ],
    [
        'name' => 'ItemManager',
        'description' => 'Manages items',
        'type' => 'role',
        'updated_at' => 1740522731,
        'created_at' => 1740334057,
    ],
];
