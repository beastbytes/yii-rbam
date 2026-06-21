<?php

declare(strict_types=1);

/**
 * @var Flash $flash
 * @var WebView $this
 */

use Yiisoft\Html\Html;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\View\WebView;

$this->registerCss(
    '.flashes {
    display: flex;
    flex-direction: column;
        justify-content: space-between;

        .flash {            
            --flash-error: oklch(0.638 0.212 32.357); 
            --flash-info: oklch(0.7156 0.1136 224.1826);
            --flash-success: oklch(0.759 0.19 131.803);
            --flash-warning: oklch(0.7285 0.1622 57.4256);
            --flash-text: oklch(0.975 0.013 244.251);
        
            border-radius: 0.5rem;
            color: var(--flash-text);
            font-family: "trebuchet ms", verdana, arial, sans-serif;
            opacity: 1;
            margin: 1em 3em;
            padding: 1.5rem 0.75rem;
            text-align: center;

            &.error {
                background-color: var(--flash-error);
            }

            &.info {
                background-color: var(--flash-info);
            }

            &.success {
                background-color: var(--flash-success);
            }

            &.warning {
                background-color: var(--flash-warning);
            }
        }
    }'
);

echo Html::div()
    ->class('flashes')
    ->open()
;
    foreach ($flash->getAll() as $key => $messages):
        foreach ($messages as $message):
            echo Html::div($message)
                ->class('flash', $key)
            ;
        endforeach;
    endforeach;
echo Html::div()
    ->close()
;