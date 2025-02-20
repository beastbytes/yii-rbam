<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Form;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

final class AssignmentForm extends FormModel
{
    #[Required]
    #[StringValue]
    #[Regex('/^([A-Z][a-zA-Z0-9]*)+/')]
    private string $roleName = '';
    #[Required]
    #[StringValue]
    private string $userId = '';

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}