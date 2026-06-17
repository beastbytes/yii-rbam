<?php

namespace BeastBytes\Yii\Rbam\Alpine\Modal;

use BeastBytes\Yii\Rbam\Alpine\AlpineAsset;
use Yiisoft\Assets\AssetBundle;

class ModalAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $css = ['modal.css'];
    public array $depends = [AlpineAsset::class];
    public ?string $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'Assets';
}