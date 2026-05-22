<?php

namespace BeastBytes\Yii\Rbam\Alpine;

use Yiisoft\Assets\AssetBundle;

final class AlpineComponentAsset extends AssetBundle
{
    public bool $cdn = true;
    public array $js = [
        'https://unpkg.com/@alpinejs/ui@3.15.8/dist/cdn.min.js',
        'https://unpkg.com/@alpinejs/focus@3.15.8/dist/cdn.min.js',
        'https://unpkg.com/alpinejs@3.15.8/dist/cdn.min.js',
    ];
    public array $jsOptions = [
        'defer' => true,
    ];
}