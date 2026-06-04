<?php

declare(strict_types=1);

/**
 * @var string $content
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Widgets\Breadcrumbs;
?>

<header class="header rbam">
    <div class="header-inner">
        <h1><?= $translator->translate(id: 'title.rbam', category: 'rbam') ?></h1>
            <?= Breadcrumbs::widget()
                ->items($this->getParameter('breadcrumbs'))
                ->render()
            ?>
    </div>

    <?php if ($this->hasBlock('rbam-menu')): ?>
        <?= $this->getBlock('rbam-menu'); ?>
    <?php endif; ?>
</header>