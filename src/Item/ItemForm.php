<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Item;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

final class ItemForm extends FormModel implements PropertyTranslatorProviderInterface
{
    public const string MODE_CREATE = 'create';
    public const string MODE_UPDATE = 'update';
    public const string NAME_REGEX = '([a-zA-Z0-9]+[ ._\-]?)+';

    #[StringValue]
    private string $description = '';
    #[Required]
    #[StringValue]
    #[Regex(pattern: '/^' . self::NAME_REGEX . '$/')]
    #[Callback(method: 'unique')]
    private string $name = '';
    #[StringValue]
    private string $ruleName = '';


    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly string $mode,
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

    public function isCreate(): bool
    {
        return $this->mode === self::MODE_CREATE;
    }

    public function unique(string $value): Result
    {
        if ($this->isCreate() && $this->itemsStorage->exists($value)) {
            return (new Result())
                ->addError($this->translator->translate('message.error.not-unique', ['item' => $value]))
            ;
        }

        return new Result();
    }
}