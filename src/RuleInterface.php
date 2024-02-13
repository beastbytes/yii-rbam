<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

interface RuleInterface extends \Yiisoft\Rbac\RuleInterface
{
    public function getCode(): string;
    public function getDescription(): string;
    public function getName(): string;
}
