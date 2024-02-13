<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Form;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\AttributeTranslator\TranslatorAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

final class RuleForm extends FormModel implements AttributeTranslatorProviderInterface
{
    #[Required]
    #[StringValue]
    private string $code = '';
    #[Required]
    #[StringValue]
    private string $description = '';
    #[Required]
    #[StringValue]
    #[Regex('/^([A-Z][a-zA-Z0-9]*)+/')]
    private string $name = '';

    public function __construct(
        private TranslatorInterface $translator
    )
    {
    }

    public function getAttributeLabel(string $attribute): string
    {
        return $this
            ->translator
            ->translate(parent::getAttributeLabel($attribute))
       ;
    }

    #[ArrayShape([
        'code' => 'string',
        'description' => 'string',
        'name' => 'string',
    ])]
    public function getAttributeLabels(): array
    {
        return [
            'code' => 'label.code',
            'description' => 'label.description',
            'name' => 'label.name',
        ];
    }

    public function getAttributeTranslator(): ?AttributeTranslatorInterface
    {
        return new TranslatorAttributeTranslator($this->translator);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
