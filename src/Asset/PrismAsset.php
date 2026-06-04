<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Asset;

use Yiisoft\Assets\AssetBundle;

class PrismAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $css = [
        'css/prism.css',
    ];
    public array $js = [
        'js/prism.js',
    ];
    public ?string $sourcePath = '@assetsSource';
}