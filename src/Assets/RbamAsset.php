<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Assets;

use Yiisoft\Assets\AssetBundle;

class RbamAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public array $css = [
        'css/rbam.css',
    ];
    public array $js = [
        'js/rbam.js',
    ];
    public ?string $sourcePath = '@assetsSource';
}