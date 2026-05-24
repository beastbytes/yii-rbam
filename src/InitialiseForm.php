<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;

final class InitialiseForm extends FormModel implements PropertyTranslatorProviderInterface
{
    #[Required]
    #[StringValue]
    private string $userId = '';

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    #[ArrayShape([
        'userId' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'userId' => $this->translator->translate('label.user-id'),
        ];
    }

    #[ArrayShape([
        'userId' => 'string',
    ])]
    public function getPropertyHints(): array
    {
        return [
            'userId' => $this->translator->translate('hint.user-id'),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}