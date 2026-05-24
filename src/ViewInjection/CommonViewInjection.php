<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\ViewInjection;

use BeastBytes\Yii\Rbam\RbamParameters;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Assets\AssetManager;
use Yiisoft\I18n\Locale;
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
        private Inflector $inflector,
        private Locale $locale,
        private RbamParameters $rbamParameters,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[ArrayShape([
        'assetManager' => AssetManager::class,
        'currentUser' => CurrentUser::class,
        'inflector' => Inflector::class,
        'locale' => Locale::class,
        'rbamParameters' => RbamParameters::class,
        'translator' => TranslatorInterface::class,
        'urlGenerator' => UrlGeneratorInterface::class
    ])]
    public function getCommonParameters(): array
    {
        return [
            'assetManager' => $this->assetManager,
            'currentUser' => $this->currentUser,
            'inflector' => $this->inflector,
            'locale' => $this->locale,
            'rbamParameters' => $this->rbamParameters,
            'translator' => $this->translator,
            'urlGenerator' => $this->urlGenerator,
        ];
    }
}
