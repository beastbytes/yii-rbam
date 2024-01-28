<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate('title.rbam'));

$breadcrumbs = [
    $translator->translate('label.rbam')
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>
<?php foreach ([Item::TYPE_ROLE, Item::TYPE_PERMISSION] as $type): ?>
    <div>
        <?php $type = $type . 's'; ?>
        <a href="<?= $urlGenerator->generate(
            'rbam.itemIndex',
            ['type' => $type]
        ) ?>">
            <?= $translator->translate('label.' . $type) ?>
        </a>
    </div>
<?php endforeach; ?>
<div>
    <a href="<?= $urlGenerator->generate('rbam.ruleIndex') ?>"><?= $translator->translate('label.rules')?></a>
</div>
<div>
    <a href="<?= $urlGenerator->generate('rbam.userIndex') ?>"><?= $translator->translate('label.users')?></a>
</div>
