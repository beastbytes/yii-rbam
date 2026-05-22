<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Rule;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

abstract class RuleForm extends FormModel implements PropertyTranslatorProviderInterface
{
    #[Required]
    #[StringValue]
    protected string $code = '';
    #[Required]
    #[StringValue]
    protected string $description = '';
    #[Required]
    #[StringValue]
    #[Regex('/^([A-Z][a-zA-Z0-9]*)+/')]
    #[Callback(method: 'unique')]
    protected string $name = '';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ?RuleServiceInterface $ruleService = null,
    )
    {
    }

    public function getCode(): string
    {
        return mb_rtrim($this->code);
    }

    public function getDescription(): string
    {
        return mb_trim($this->description);
    }

    public function getName(): string
    {
        return mb_trim($this->name);
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

    public function isCreate(): bool
    {
        return $this instanceof CreateRuleForm;
    }

    public function isUpdate(): bool
    {
        return $this instanceof UpdateRuleForm;
    }

    public function unique(string $value): Result
    {
        if ($this->ruleService instanceof RuleServiceInterface && !$this->ruleService->isUnique($value)) {
            return (new Result())
                ->addError($this->translator->translate('message.error.not-unique', ['item' => $value]))
            ;
        }

        return new Result();
    }
}