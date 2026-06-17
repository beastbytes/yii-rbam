<?php

namespace BeastBytes\Yii\Rbam\Alpine\Tabs;

use BeastBytes\Yii\Rbam\Alpine\AlpineAsset;
use Yiisoft\Assets\AssetBundle;

class TabsAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $css = ['tabs.css'];
    public array $depends = [AlpineAsset::class];
    public ?string $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'Assets';
}