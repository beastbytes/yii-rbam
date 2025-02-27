<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Assets;

use BeastBytes\Yii\Widgets\Assets\DialogAsset;
use Yiisoft\Assets\AssetBundle;

class RemoveAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $js = [
        'js/remove.js',
    ];
    public ?string $sourcePath = '@assetsSource';
    public array $depends = [
        DialogAsset::class
    ];
}