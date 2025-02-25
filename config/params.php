<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

return [
    'beastbytes/yii-rbam' => [
        'buttons' => [ // allows use of icon fonts and/or css frameworks to style buttons
            'cancel' => [
                'attributes' => ['class' => 'btn btn_cancel'],
                'content' => 'button.cancel',
            ],
            'createPermission' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.create-permission',
            ],
            'createRole' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.create-role',
            ],
            'createRule' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.create-rule',
            ],
            'done' => [
                'attributes' => ['class' => 'btn btn_done'],
                'content' => 'button.done',
            ],
            'manageChildRoles' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.manage-child-roles',
            ],
            'managePermissions' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.manage-permissions',
            ],
            'manageRoleAssignments' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.manage-role-assignments',
            ],
            'remove' => [
                'attributes' => ['class' => 'btn btn_remove'],
                'content' => 'button.remove',
            ],
            'removeAll' => [
                'attributes' => ['class' => 'btn btn_remove-all'],
                'content' => 'button.remove-all',
            ],
            'revokeAll' => [
                'attributes' => ['class' => 'btn btn_revoke-all'],
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
                    'stroke' => 'hsl(195, 66%, 55%)',
                ],
                'rect' => [
                    'fill' => 'hsl(195, 66%, 90%)',
                    'stroke' => 'hsl(195, 66%, 55%)',
                    'stroke-width' => '1',
                ],
            ],
            'ancestor_role' => [
                'line.divider' => [
                    'stroke' => 'hsl(195, 66%, 55%)',
                ],
                'rect' => [
                    'fill' => 'hsl(195, 66%, 90%)',
                    'stroke' => 'hsl(195, 66%, 55%)',
                    'stroke-width' => '1',
                ],
            ],
            'current_permission' => [
                'line.divider' => [
                    'stroke' => 'hsl(88, 60%, 49%)',
                ],
                'rect' => [
                    'fill' => 'hsl(88, 60%, 90%)',
                    'stroke' => 'hsl(88, 60%, 49%)',
                    'stroke-width' => '1',
                ],
            ],
            'current_role' => [
                'line.divider' => [
                    'stroke' => 'hsl(88, 60%, 49%)',
                ],
                'rect' => [
                    'fill' => 'hsl(88, 60%, 90%)',
                    'stroke' => 'hsl(88, 60%, 49%)',
                    'stroke-width' => '2',
                ],
            ],
            'descendant_permission' => [
                'line.divider' => [
                    'stroke' => 'hsl(29, 88%, 55%)',
                ],
                'rect' => [
                    'fill' => 'hsl(29, 88%, 90%)',
                    'stroke' => 'hsl(29, 88%, 55%)',
                    'stroke-width' => '1',
                ],
            ],
            'descendant_role' => [
                'line.divider' => [
                    'stroke' => 'hsl(29, 88%, 55%)',
                ],
                'rect' => [
                    'fill' => 'hsl(29, 88%, 90%)',
                    'stroke' => 'hsl(29, 88%, 55%)',
                    'stroke-width' => '1',
                ],
            ],
        ]
    ],
];