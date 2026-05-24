<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\ViewInjection;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Yii\View\Renderer\LayoutParametersInjectionInterface;

final readonly class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    public function __construct(
        private Flash $flash
    ) {
    }

    #[ArrayShape([
        'flash' => Flash::class
    ])]
    public function getLayoutParameters(): array
    {
        return [
            'flash' => $this->flash,
        ];
    }
}
