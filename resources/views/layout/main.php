<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var string $content
 * @var WebView $this
 * @var TranslatorInterface $translator;
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$assetManager->register(RbamAsset::class);
$this->addCssFiles($assetManager->getCssFiles());
$this->addJsFiles($assetManager->getJsFiles());

$this->beginPage()
?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($this->getTitle()) ?></title>
        <?php $this->head() ?>
    </head>
    <body class='rbam'>
    <?php $this->beginBody() ?>
    <?= $this->render('../layout/header') ?>

    <div class="content">
        <?= $content ?>
    </div>

    <?= $this->render('../layout/flash') ?>
    <?= $this->render('../layout/footer') ?>
    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>