<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Form\RuleForm;
use Yiisoft\Rbac\RuleInterface;

Interface RuleServiceInterface
{
    public function getRuleNames(): array;

    public function getRule(string $name): ?RbamRuleInterface;

    public function save(RuleForm $model, ?string $previousName = null): bool;
}