<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\ViewInjection;

use BeastBytes\Yii\Rbam\Alpine\FieldFactory;
use BeastBytes\Yii\Rbam\RbamParameters;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Assets\AssetManager;
use Yiisoft\I18n\LocaleProvider;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\CommonParametersInjectionInterface;

final readonly class CommonViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(
        private AssetManager $assetManager,
        private CurrentUser $currentUser,
        private FieldFactory $fieldFactory,
        private Inflector $inflector,
        private LocaleProvider $localeProvider,
        private RbamParameters $rbamParameters,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[ArrayShape([
        'assetManager' => AssetManager::class,
        'currentUser' => CurrentUser::class,
        'fieldFactory' => FieldFactory::class,
        'inflector' => Inflector::class,
        'localeProvider' => LocaleProvider::class,
        'rbamParameters' => RbamParameters::class,
        'translator' => TranslatorInterface::class,
        'urlGenerator' => UrlGeneratorInterface::class
    ])]
    public function getCommonParameters(): array
    {
        return [
            'assetManager' => $this->assetManager,
            'currentUser' => $this->currentUser,
            'fieldFactory' => $this->fieldFactory,
            'inflector' => $this->inflector,
            'localeProvider' => $this->localeProvider,
            'rbamParameters' => $this->rbamParameters,
            'translator' => $this->translator,
            'urlGenerator' => $this->urlGenerator,
        ];
    }
}
