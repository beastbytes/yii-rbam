<?php

declare(strict_types=1);

/**
 * @var Flash $flash
 * @var WebView $this
 */

use Yiisoft\Html\Html;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\View\WebView;

echo Html::div()->attributes(['class' => "flashes"])->open();
    foreach ($flash->getAll() as $key => $messages):
        foreach ($messages as $message):
            echo Html::div($message, ['class' => "flash $key"]);
        endforeach;
    endforeach;
echo Html::div()->close();