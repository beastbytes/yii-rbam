<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var string $content
 * @var WebView $this
 * @var TranslatorInterface $translator;
 */

use BeastBytes\Yii\Rbam\Assets\PaginationAsset;
use BeastBytes\Yii\Rbam\Alpine\AlpineComponentAsset;
use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$assetManager->register(AlpineComponentAsset::class);
$assetManager->register(PaginationAsset::class);
$assetManager->register(RbamAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addJsFiles($assetManager->getJsFiles());
$this->registerJs('var paginators = []', WebView::POSITION_HEAD);

$this->beginPage()
?>
<!doctype html>
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

    <div x-data="{open: false, detail: {}}" class="modal" id="modal" @modal.window="detail = $event.detail; open=true;">
        <div x-dialog x-model="open" x-cloak class="dialog">
            <div x-dialog:overlay x-transition.opacity class="overlay">
                <div x-dialog:panel x-transition class="panel">
                    <button type="button" @click="$dialog.close()" class="close-button">
                        <span x-text="detail.closeDialog" class="sr-only"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" stroke="white" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"></path>
                        </svg>
                    </button>
                    <div x-dialog:title x-html="detail.title" class="title"></div>
                    <div x-html="detail.content" class="content"></div>
                    <div class="footer">
                        <button type="button" @click="rbam.action($data.detail.buttons.continue)" class="btn btn_continue">
                            <?= $translator->translate('button.continue') ?>
                        </button>
                        <button type="button" @click="$dialog.close()" class="btn btn_cancel">
                            <?= $translator->translate('button.cancel') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>