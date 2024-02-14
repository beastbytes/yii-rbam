<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\ViewInjection;

use BeastBytes\Yii\Rbam\RbamParameters;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Assets\AssetManager;
use Yiisoft\I18n\Locale;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

final class CommonViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(
        private readonly AssetManager $assetManager,
        private readonly Inflector $inflector,
        private readonly Locale $locale,
        private readonly ManagerInterface $rbacManager,
        private readonly RbamParameters $rbamParameters,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[ArrayShape([
        'assetManager' => AssetManager::class,
        'inflector' => Inflector::class,
        'locale' => Locale::class,
        'rbacManager' => ManagerInterface::class,
        'rbamParameters' => RbamParameters::class,
        'translator' => TranslatorInterface::class,
        'urlGenerator' => UrlGeneratorInterface::class
    ])]
    public function getCommonParameters(): array
    {
        return [
            'assetManager' => $this->assetManager,
            'inflector' => $this->inflector,
            'locale' => $this->locale,
            'rbacManager' => $this->rbacManager,
            'rbamParameters' => $this->rbamParameters,
            'translator' => $this->translator,
            'urlGenerator' => $this->urlGenerator,
        ];
    }
}
