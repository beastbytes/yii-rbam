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

<header>
    <div class="header-inner">
        <h1><?= $translator->translate('title.rbam') ?></h1>
        <?php if ($this->hasParameter('breadcrumbs')): ?>
            <?= Breadcrumbs::widget()
                ->items($this->getParameter('breadcrumbs'))
                ->render()
            ?>
        <?php endif; ?>
    </div>

    <?php if ($this->hasBlock('block-menu')):
        echo $this->getBlock('block-menu');
    endif; ?>
</header>