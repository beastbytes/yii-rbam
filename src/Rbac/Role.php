<?php

namespace BeastBytes\Yii\Rbam\Rbac;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix;

#[Prefix('rbam', '.')]
enum Role: string implements ItemInterface
{
    use ItemTrait;

    case admin = 'admin';
    case itemManager = 'item.manager';
    case ruleManager = 'rule.manager';
    case userManager = 'user.manager';
}