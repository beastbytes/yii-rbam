<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;

$this->setTitle('RBAM');

$breadcrumbs = [
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= $translator->translate('title.rbam') ?></h1>
<?php foreach ([Item::TYPE_ROLE, Item::TYPE_PERMISSION] as $type): ?>
    <div>
        <?php $type = $type . 's'; ?>
        <a href="<?= $urlGenerator->generate(
            'rbam.itemIndex',
            ['type' => $type]
        ) ?>">
            <?= ucfirst($type) ?>
        </a>
    </div>
<?php endforeach; ?>
<div>
    <a href="<?= $urlGenerator->generate('rbam.ruleIndex') ?>">Rules</a>
</div>
<div>
    <a href="<?= $urlGenerator->generate('rbam.userIndex') ?>">Users</a>
</div>
