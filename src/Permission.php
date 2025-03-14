<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

enum Permission: string
{
    case RbamItemCreate = 'item-create';
    case RbamItemRemove = 'item-remove';
    case RbamItemUpdate = 'item-update';
    case RbamItemView = 'item-view';
    case RbamIndex = 'rbam-index';
    case RbamRuleCreate = 'rule-create';
    case RbamRuleDelete = 'rule-delete';
    case RbamRuleUpdate = 'rule-update';
    case RbamRuleView = 'rule-view';
    case RbamUserUpdate = 'user-update';
    case RbamUserView = 'user-view';
}