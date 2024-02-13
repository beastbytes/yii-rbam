<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\ViewInjection;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;

final class LayoutViewInjection implements LayoutParametersInjectionInterface
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
