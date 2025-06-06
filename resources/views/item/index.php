<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var int $currentPage
 * @var Inflector $inflector
 * @var array $items
 * @var ItemsStorageInterface $itemsStorage
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate('label.' . $type . 's'));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= $this->render(
'_items',
    [
        'actionButtons' => ['view', 'remove'],
        'currentPage' => $currentPage,
        'emptyText' => 'label.no-' . $type . 's-found',
        'header' => $this->getTitle(),
        'item' => null,
        'items' => $items,
        'itemsStorage' => $itemsStorage,
        'toolbar' => Html::a(
            content: $translator->translate($rbamParameters->getButtons('create' . ucfirst($type))['content']),
            url: $urlGenerator->generate('rbam.createItem', ['type' => $type]),
            attributes: $rbamParameters->getButtons('create' . ucfirst($type))['attributes'],
        )
            ->render()
        ,
        'translator' => $translator,
        'type' => $type,
        'urlGenerator' => $urlGenerator,
        'user' => null,

    ]
)
?>