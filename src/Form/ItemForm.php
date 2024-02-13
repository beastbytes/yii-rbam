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

final class ItemForm extends FormModel implements AttributeTranslatorProviderInterface
{
    #[Required]
    #[StringValue]
    private string $description = '';
    #[Required]
    #[StringValue]
    #[Regex('/^([A-Z][a-z0-9]*)+$/')]
    private string $name = '';
    #[StringValue]
    private string $ruleName = '';

    public function __construct(
        private readonly TranslatorInterface $translator
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
        'description' => 'string',
        'name' => 'string',
        'ruleName' => 'string',
    ])]
    public function getAttributeLabels(): array
    {
        return [
            'description' => 'label.description',
            'name' => 'label.name',
            'ruleName' => 'label.ruleName',
        ];
    }

    public function getAttributeTranslator(): ?AttributeTranslatorInterface
    {
        return new TranslatorAttributeTranslator($this->translator);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRuleName(): string
    {
        return $this->ruleName;
    }
}
