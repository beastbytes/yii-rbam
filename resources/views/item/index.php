<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var int $currentPage
 * @var Inflector $inflector
 * @var array $items
 * @var int $pageSize
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle(
    $translator->translate('label.' . $type . 's')
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= $this->render(
'_items',
    [
        'actionButtons' => ['view', 'remove'],
        'dataReader' => (new OffsetPaginator(new IterableDataReader($items)))
            ->withCurrentPage($currentPage)
            ->withPageSize($pageSize)
        ,
        'layout' => "{toolbar}\n{items}",
        'toolbar' => Html::a(
            content: $translator->translate($rbamParameters->getButtons('add' . ucfirst($type))['content']),
            url: $urlGenerator->generate('rbam.addItem', ['type' => $type]),
            attributes: $rbamParameters->getButtons('add' . ucfirst($type))['attributes'],
        ),
        'translator' => $translator,
        'type' => $type,
        'urlGenerator' => $urlGenerator,
    ]
)
?>
