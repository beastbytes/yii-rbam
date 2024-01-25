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
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView;

$this->setTitle($translator->translate('title.users'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= ListView::widget()
    ->dataReader(new IterableDataReader($users))
    ->webView($this)
    ->emptyText($translator->translate('message.no_users_found'))
    ->itemView(static function (UserInterface $user) use ($urlGenerator) {
        return Html::a(
            content: $user->getName(),
            url: $urlGenerator->generate('rbam.viewUser', ['id' => $user->getId()])
        )
        ->render();
    })
?>
