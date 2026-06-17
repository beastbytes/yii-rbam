<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var string $content
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\Asset\PaginationAsset;
use BeastBytes\Yii\Rbam\Alpine\AlpineAsset;
use BeastBytes\Yii\Rbam\Asset\RbamAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Widgets\ContentDecorator;

$assetManager->register(AlpineAsset::class);
$assetManager->register(PaginationAsset::class);
$assetManager->register(RbamAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addJsFiles($assetManager->getJsFiles());

$this->registerJs('var paginators = []', WebView::POSITION_HEAD);

$this->setBlock('rbam-header', $this->render('./header'));

ContentDecorator::widget()
    ->view($this, $rbamParameters->getApplicationLayout())
    ->begin()
;
?>

<div class='rbam'>
    <?= $content ?>
</div>

<?= ContentDecorator::end(); ?>