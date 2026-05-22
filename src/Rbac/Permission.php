<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rbac;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Prefix;

#[Prefix('rbam', '.')]
enum Permission: string implements ItemInterface
{
    use ItemTrait;

    case index = 'index';
    case itemCreate = 'item.create';
    case itemRemove = 'item.remove';
    case itemUpdate = 'item.update';
    case itemView = 'item.view';
    case ruleCreate = 'rule.create';
    case ruleDelete = 'rule.delete';
    case ruleUpdate = 'rule.update';
    case ruleView = 'rule.view';
    case userUpdate = 'user.update';
    case userView  = 'user.view';
}