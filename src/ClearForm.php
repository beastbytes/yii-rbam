<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;

final class ClearForm extends FormModel implements PropertyTranslatorProviderInterface
{
    #[Required]
    #[StringValue]
    #[Compare(targetProperty: 'authCode', type: CompareType::STRING)]
    private string $code = '';

    public function __construct(private readonly TranslatorInterface $translator, private readonly string $authCode)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    #[ArrayShape([
        'code' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'code' => $this->translator->translate('label.code'),
        ];
    }

    #[ArrayShape([
        'code' => 'string',
    ])]
    public function getPropertyHints(): array
    {
        return [
            'code' => $this->translator->translate('hint.code', ['authCode' => $this->authCode]),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}