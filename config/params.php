<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

//use BeastBytes\Yii\Rbam\RulesMiddleware;
//@todo think rules need handling differently
$rules = [];
$ruleFiles = array_slice(
    scandir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'rbac' . DIRECTORY_SEPARATOR . '_rules'),
    2
);

foreach ($ruleFiles as $ruleFile) {
    $rules[lcfirst(substr($ruleFile, 0, -8))]
        = 'BeastBytes\\Yii\\Rbam\\Rule\\' . substr($ruleFile, 0, -4)
    ;
}

return [
    'beastbytes/yii-rbam' => [
        'buttons' => [ // allows use of icon fonts and/or css frameworks to style buttons
            'createPermission' => [
                'attributes' => ['class' => 'btn btn_create btn_create_permission'],
                'content' => 'button.create-permission',
            ],
            'createRole' => [
                'attributes' => ['class' => 'btn btn_create btn_create_role'],
                'content' => 'button.create-role',
            ],
            'createRule' => [
                'attributes' => ['class' => 'btn btn_create btn_create_rule'],
                'content' => 'button.create-rule',
            ],
            'done' => [
                'attributes' => ['class' => 'btn btn_done'],
                'content' => 'button.done',
            ],
            'manageChildRoles' => [
                'attributes' => ['class' => 'btn btn_manage_child_roles'],
                'content' => 'button.manage-child-roles',
            ],
            'managePermissions' => [
                'attributes' => ['class' => 'btn btn_manage_permissions'],
                'content' => 'button.manage-permissions',
            ],
            'manageRoleAssignments' => [
                'attributes' => ['class' => 'btn btn_manage_role_assignments'],
                'content' => 'button.manage-role-assignments',
            ],
            'remove' => [
                'attributes' => ['class' => 'btn btn_remove'],
                'content' => 'button.remove',
            ],
            'removeAll' => [
                'attributes' => ['class' => 'btn btn_remove_all'],
                'content' => 'button.remove-all',
            ],
            'revokeAll' => [
                'attributes' => ['class' => 'btn btn_revoke_all'],
                'content' => 'button.revoke-all',
            ],
            'submit' => [
                'attributes' => ['class' => 'btn btn_submit'],
                'content' => 'button.submit',
            ],
            'update' => [
                'attributes' => ['class' => 'btn btn_update'],
                'content' => 'button.update',
            ],
            'view' => [
                'attributes' => ['class' => 'btn btn_view'],
                'content' => 'button.view',
            ],
        ],
        'datetimeFormat' => 'Y-m-d H:i:s',
        'defaultRoles' => [],
        'mermaidDiagramStyles' => [
            'ancestor_permission' => [
                'line.divider' => [
                    'stroke' => '#1A6256',
                ],
                'rect' => [
                    'fill' => '#C0DCD7',
                    'stroke' => '#1A6256',
                    'stroke-width' => '1',
                ],
            ],
            'ancestor_role' => [
                'line.divider' => [
                    'stroke' => '#1A6256',
                ],
                'rect' => [
                    'fill' => '#C0DCD7',
                    'stroke' => '#1A6256',
                    'stroke-width' => '2',
                ],
            ],
            'current_permission' => [
                'line.divider' => [
                    'stroke' => '#378223',
                ],
                'rect' => [
                    'fill' => '#D8F0D1',
                    'stroke' => '#378223',
                    'stroke-width' => '1',
                ],
            ],
            'current_role' => [
                'line.divider' => [
                    'stroke' => '#378223',
                ],
                'rect' => [
                    'fill' => '#D8F0D1',
                    'stroke' => '#378223',
                    'stroke-width' => '2',
                ],
            ],
            'descendant_permission' => [
                'line.divider' => [
                    'stroke' => '#761F5A',
                ],
                'rect' => [
                    'fill' => '#E8CBDF',
                    'stroke' => '#761F5A',
                    'stroke-width' => '1',
                ],
            ],
            'descendant_role' => [
                'line.divider' => [
                    'stroke' => '#761F5A',
                ],
                'rect' => [
                    'fill' => '#E8CBDF',
                    'stroke' => '#761F5A',
                    'stroke-width' => '2',
                ],
            ],
        ]
    ],
    /*
    'middlewares' => [
        RulesMiddleware::class
    ],
    */
    'yiisoft/rbac-rules-container' => [
        'rules' => $rules,
        'validate' => false,
    ],
];