<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;

final class RbamForm extends FormModel implements PropertyTranslatorProviderInterface
{

    #[Required]
    private string $id = '';
    #[Required]
    #[StringValue]
    protected string $password = '';
    #[Required]
    #[StringValue]
    #[Compare(targetProperty: 'password')]
    protected string $passwordRpt = '';


    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return (new PasswordHasher(PASSWORD_BCRYPT))->hash($this->password);
    }

    #[ArrayShape([
        'id' => 'string',
        'password' => 'string',
        'passwordRpt' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'id' => $this->translator->translate('label.id'),
            'password' => $this->translator->translate('label.password'),
            'passwordRpt' => $this->translator->translate('label.password-rpt'),
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'id' => $this->translator->translate('hint.id'),
            'password' => $this->translator->translate('hint.password'),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}