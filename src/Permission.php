<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

enum Permission
{
    case ItemCreate;
    case ItemRemove;
    case ItemUpdate;
    case ItemView;
    case RbamIndex;
    case RuleCreate;
    case RuleDelete;
    case RuleUpdate;
    case RuleView;
    case UserView;
}