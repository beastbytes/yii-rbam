<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

enum Permission: string
{
    case ItemCreate = 'item-create';
    case ItemRemove = 'item-remove';
    case ItemUpdate = 'item-update';
    case ItemView = 'item-view';
    case RbamIndex = 'rbam-index';
    case RuleCreate = 'rule-create';
    case RuleDelete = 'rule-delete';
    case RuleUpdate = 'rule-update';
    case RuleView = 'rule-view';
    case UserView = 'user-view';
}