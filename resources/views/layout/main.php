<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Widgets\Breadcrumbs;

/**
 * @var Flash $flash
 * @var string $content
 * @var WebView $this
 * @var Translator $translator;
 */

$this->beginPage()
?><!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->getTitle()) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php foreach ($flash->getAll() as $key => $messages): ?>
    <?php foreach ($messages as $message): ?>
        <?= Html::div($message, ['class' => "flash $key"]) ?>
    <?php endforeach; ?>
<?php endforeach; ?>

<?php if ($this->hasParameter('breadcrumbs')): ?>
<?= Breadcrumbs::widget()
    ->items($this->getParameter('breadcrumbs'))
    ->render()
?>
<?php endif; ?>

<div class="content">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


