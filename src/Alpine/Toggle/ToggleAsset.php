<?php

namespace BeastBytes\Yii\Rbam\Alpine\Toggle;

use BeastBytes\Yii\Rbam\Alpine\AlpineAsset;
use Yiisoft\Assets\AssetBundle;

class ToggleAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $css = ['toggle.css'];
    public array $depends = [AlpineAsset::class];
    public ?string $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'Assets';
}