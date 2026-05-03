<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Assets;

use Yiisoft\Assets\AssetBundle;

class PaginationAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $js = [
        'js/paginator.js',
    ];
    public ?string $sourcePath = '@assetsSource';
    //public ?string $sourcePath = __DIR__ . '/../resources/assets';
}
