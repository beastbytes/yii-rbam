<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\StringValue;

final class Translation extends FormModel
{

    public function __construct(
        #[StringValue]
        private readonly string $description = '',
        private readonly string $locale = '',
    )
    {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}