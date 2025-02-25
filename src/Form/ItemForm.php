<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Form;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

final class ItemForm extends FormModel implements PropertyTranslatorProviderInterface
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

    #[ArrayShape([
        'description' => 'string',
        'name' => 'string',
        'ruleName' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'description' => $this->translator->translate('label.description'),
            'name' => $this->translator->translate('label.name'),
            'ruleName' => $this->translator->translate('label.rule'),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}