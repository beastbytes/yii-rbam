<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

enum Permission: string
{
    case RbacItemCreate = 'item-create';
    case RbacItemRemove = 'item-remove';
    case RbacItemUpdate = 'item-update';
    case RbacItemView = 'item-view';
    case RbamIndex = 'rbam-index';
    case RbacRuleCreate = 'rule-create';
    case RbacRuleDelete = 'rule-delete';
    case RbacRuleUpdate = 'rule-update';
    case RbacRuleView = 'rule-view';
    case RbacUserUpdate = 'user-update';
    case RbacUserView = 'user-view';
}