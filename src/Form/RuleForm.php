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

final class RuleForm extends FormModel implements PropertyTranslatorProviderInterface
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
        private readonly TranslatorInterface $translator
    )
    {
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

    #[ArrayShape([
        'code' => 'string',
        'description' => 'string',
        'name' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'code' => $this->translator->translate('label.code'),
            'description' => $this->translator->translate('label.description'),
            'name' => $this->translator->translate('label.name'),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}