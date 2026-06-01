<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Item;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

final class Translation extends FormModel
{

    public function __construct(
        #[StringValue]
        private readonly string $name = '',
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}