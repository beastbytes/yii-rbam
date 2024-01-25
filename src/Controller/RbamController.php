<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\View\ViewRenderer;

class RbamController
{
    public function __construct(
        private ViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withViewPath('@views/rbam')
        ;
    }

    public function index(): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render('index')
        ;
    }
}
