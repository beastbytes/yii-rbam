<?php

namespace BeastBytes\Yii\Rbam\Alpine;

use BeastBytes\Yii\Rbam\Alpine\Toggle\Toggle;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\FormModelInterface;

class FieldFactory extends \Yiisoft\FormModel\FieldFactory
{
    final public function toggle(
        FormModelInterface $formModel,
        string $property,
        array $config = [],
        ?string $theme = null,
    ): Toggle {
        return Toggle::widget(config: $config, theme: $theme ?? $this->defaultTheme)
            ->inputData(new FormModelInputData($formModel, $property))
        ;
    }
}