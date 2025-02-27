<?php

declare(strict_types=1);

/**
 * @var string $content
 * @var TranslatorInterface $translator
 * @var WebView $this
 */

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Widgets\Breadcrumbs;

?>

<header>
    <h1><?= $translator->translate('title.rbam') ?></h1>
    <?php if ($this->hasParameter('breadcrumbs')): ?>
        <?= Breadcrumbs::widget()
            ->items($this->getParameter('breadcrumbs'))
            ->render()
        ?>
    <?php endif; ?>
</header>