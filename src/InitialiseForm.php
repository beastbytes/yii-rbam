<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\ValidationContext;

final class InitialiseForm extends FormModel implements PropertyTranslatorProviderInterface
{
    #[StringValue]
    private string $except = './config/**, ./resources/**, ./tests/**, ./vendor/**';
    #[BooleanValue]
    private bool $initialiseApplication = false;
    #[StringValue]
    private string $only = '**Controller.php';
    #[StringValue]
    #[Callback(method: 'validateSrcDir')]
    private string $srcDir = './src';
    #[Required]
    #[StringValue]
    private string $userId = '';

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getExcept(): string
    {
        return $this->except;
    }

    public function getOnly(): string
    {
        return $this->only;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSrcDir(): string
    {
        return $this->srcDir;
    }

    public function shouldInitialiseApplication(): bool
    {
        return $this->initialiseApplication;
    }

    #[ArrayShape([
        'except' => 'string',
        'initialiseApplication' => 'string',
        'only' => 'string',
        'srcDir' => 'string',
        'userId' => 'string',
    ])]
    public function getPropertyLabels(): array
    {
        return [
            'except' => $this->translator->translate('label.except'),
            'initialiseApplication' => $this->translator->translate('label.initialise-application'),
            'only' => $this->translator->translate('label.only'),
            'srcDir' => $this->translator->translate('label.src-dir'),
            'userId' => $this->translator->translate('label.user-id'),
        ];
    }

    #[ArrayShape([
        'except' => 'string',
        'initialiseApplication' => 'string',
        'only' => '**Controller.php',
        'srcDir' => 'string',
        'userId' => 'string',
    ])]
    public function getPropertyHints(): array
    {
        return [
            'except' => $this->translator->translate('hint.except'),
            'initialiseApplication' => $this->translator->translate('hint.initialise-application'),
            'only' => $this->translator->translate('hint.only'),
            'srcDir' => $this->translator->translate('hint.src-dir'),
            'userId' => $this->translator->translate('hint.user-id'),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }

    public function validateSrcDir(string $value): Result
    {
        if ($this->initialiseApplication) {
            $srcDir = realpath(dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . $value);

            if ($srcDir === false) {
                return (new Result())
                    ->addError($this->translator->translate('message.error.src-dir-not-found', ['srcDir' => $value]))
                ;
            }

            $this->srcDir = $srcDir;
        }

        return new Result();
    }
}