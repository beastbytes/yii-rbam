<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

return [
    'beastbytes/yii-rbam' => [
        'actionButtons' => [ // 'add' is Html::a(), others are ActionButton
            'addPermission' => [
                'content' => 'button.add_permission',
                'attributes' => ['class' => 'btn btn_add btn_add_permission'],
            ],
            'addRole' => [
                'content' => 'button.add_role',
                'attributes' => ['class' => 'btn btn_add btn_add_role'],
            ],
            'addRule' => [
                'content' => 'button.add_rule',
                'attributes' => ['class' => 'btn btn_add btn_add_rule'],
            ],
            'manageRoleAssignments' => [
                'content' => 'button.manage_role_assignments',
                'attributes' => ['class' => 'btn btn_manage_role_assignments'],
            ],
            'update' => [
                'content' => 'button.update',
                'attributes' => ['class' => 'btn btn_update'],
            ],
            'view' => [
                'content' => 'button.view',
                'attributes' => ['class' => 'btn btn_view'],
            ],
        ],
        'datetimeFormat' => 'Y-m-d H:i:s',
        'defaultRoles' => [],
    ]
];
