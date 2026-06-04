<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\ViewInjection\CommonViewInjection;
use BeastBytes\Yii\Rbam\ViewInjection\LayoutViewInjection;
use Yiisoft\Definitions\Reference;
use Yiisoft\Yii\View\Renderer\CsrfViewInjection;

return [
    'beastbytes/yii-rbam' => [
        'applicationLayout' => '@layout/main',
        'buttons' => [ // allows use of icon fonts and/or CSS frameworks to style buttons
            'add' => [
                'attributes' => ['class' => 'btn btn_add'],
                'content' => 'button.add',
            ],
            'assign' => [
                'attributes' => ['class' => 'btn btn_assign'],
                'content' => 'button.role.assignment.create',
            ],
            'cancel' => [
                'attributes' => ['class' => 'btn btn_cancel'],
                'content' => 'button.cancel',
            ],
            'createPermission' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.permission.create',
            ],
            'createRole' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.role.create',
            ],
            'createRule' => [
                'attributes' => ['class' => 'btn btn_create'],
                'content' => 'button.rule.create',
            ],
            'deny' => [
                'attributes' => ['class' => 'btn btn_deny'],
                'content' => 'button.deny',
            ],
            'done' => [
                'attributes' => ['class' => 'btn btn_done'],
                'content' => 'button.done',
            ],
            'grant' => [
                'attributes' => ['class' => 'btn btn_grant'],
                'content' => 'button.grant',
            ],
            'manageChildPermissions' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.permission.child-permissions.manage',
            ],
            'manageChildRoles' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.role.child-roles.manage',
            ],
            'managePermissions' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.permission.manage',
            ],
            'manageRoleAssignments' => [
                'attributes' => ['class' => 'btn btn_manage'],
                'content' => 'button.role.assignment.manage',
            ],
            'remove' => [
                'attributes' => ['class' => 'btn btn_remove'],
                'content' => 'button.remove',
            ],
            'removeAll' => [
                'attributes' => ['class' => 'btn btn_remove-all'],
                'content' => 'button.remove-all',
            ],
            'revoke' => [
                'attributes' => ['class' => 'btn btn_revoke'],
                'content' => 'button.role.assignment.revoke',
            ],
            'revokeAll' => [
                'attributes' => ['class' => 'btn btn_revoke-all'],
                'content' => 'button.role.assignment.revoke-all',
            ],
            'submit' => [
                'attributes' => ['class' => 'btn btn_submit'],
                'content' => 'button.submit',
            ],
            'translate' => [
                'attributes' => ['class' => 'btn btn_translate'],
                'content' => 'button.translate',
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
        'diagramStyles' => [
            'ancestor_permission' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.7285 0.1622 57.4256)',
                    'font-size' => '1.125rem',
                ],
            ],
            'ancestor_role' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.7285 0.1622 57.4256)',
                    'font-size' => '1.125rem',
                ],
            ],
            'current_permission' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.759 0.19 131.803)',
                    'font-size' => '1.25rem',
                ],
            ],
            'current_role' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.759 0.19 131.803)',
                    'font-size' => '1.25rem',
                ],
            ],
            'descendant_permission' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.7156 0.1136 224.1826)',
                    'font-size' => '1.125rem',
                ],
            ],
            'descendant_role' => [
                '.label-group .nodeLabel' => [
                    'color' => 'oklch(0.7156 0.1136 224.1826)',
                    'font-size' => '1.125rem',
                ],
            ],
        ],
        'pageSize' => 20,
        'tabPageSize' => 10,
    ],
    'yiisoft/yii-view-renderer' => [
        'viewPath' => '@views',
        'layout' => '@layout/rbam',
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
        ],
    ],
];