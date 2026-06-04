<?php

declare(strict_types=1);

/**
 * @var string $content
 * @var WebView $this
 */

use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

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
    <body>
    <?php $this->beginBody() ?>
        <?php if ($this->hasBlock('rbam-header')): ?>
            <?= $this->getBlock('rbam-header') ?>
        <?php else: ?>>
            <?= $this->render('./header') ?>
        <?php endif; ?>

        <div class="content">
            <?= $content ?>
        </div>

        <?= $this->render('./flash') ?>
        <?= $this->render('./footer') ?>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>