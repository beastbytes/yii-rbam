<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\Yii\Rbam\RulesMiddleware;

$rules = [];
$ruleFiles = array_slice(
    scandir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'rbac' . DIRECTORY_SEPARATOR . 'rules'),
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
            'manageChildRoles' => [
                'content' => 'button.manage_child_roles',
                'attributes' => ['class' => 'btn btn_manage_child_roles'],
            ],
            'managePermissions' => [
                'content' => 'button.manage_permissions',
                'attributes' => ['class' => 'btn btn_manage_permissions'],
            ],
            'manageRoleAssignments' => [
                'content' => 'button.manage_role_assignments',
                'attributes' => ['class' => 'btn btn_manage_role_assignments'],
            ],
            'remove' => [
                'content' => 'button.remove',
                'attributes' => ['class' => 'btn btn_remove'],
            ],
            'removeAll' => [
                'content' => 'button.remove_all',
                'attributes' => ['class' => 'btn btn_remove_all'],
            ],
            'revokeAll' => [
                'content' => 'button.revoke_all',
                'attributes' => ['class' => 'btn btn_revoke_all'],
            ],
            'submit' => [
                'content' => 'button.submit',
                'attributes' => ['class' => 'btn btn_submit'],
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
    'middlewares' => [
        RulesMiddleware::class
    ],
    'yiisoft/rbac-rules-container' => [
        'rules' => $rules,
        'validate' => false,
    ],
];
