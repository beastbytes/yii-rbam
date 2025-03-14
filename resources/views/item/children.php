<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Item[] $ancestors
 * @var AssetManager $assetManager
 * @var Item[] $children
 * @var Item[] $descendants
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item[] $items
 * @var ManagerInterface $manager
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\ItemTypeService;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$this->setTitle($translator->translate(
    $type === Item::TYPE_PERMISSION ? 'label.manage-permissions' : 'label.manage-child-roles',
));

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.roles'),
        'url' => $urlGenerator->generate(
            'rbam.itemIndex',
            [
                'type' => $inflector->toPlural(ItemTypeService::getItemType($parent)),
            ],
        ),
    ],
    [
        'label' => $parent->getName(),
        'url' => $urlGenerator->generate(
            'rbam.viewItem',
            [
                'name' => $inflector->toSnakeCase($parent->getName()),
                'type' => ItemTypeService::getItemType($parent),
            ],
        ),
    ],
    $this->getTitle(),
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h2>
    <?= $translator->translate(
        $type === Item::TYPE_PERMISSION ? 'label.manage-permissions-for' : 'label.manage-child-roles-for',
        ['name' => $parent->getName()],
    ) ?>
</h2>

<div id="js-items" data-_csrf="<?= $csrf ?>" data-item="<?= $parent->getName() ?>" data-type="<?= $type ?>">
    <?= $this->render(
        '_children',
        [
            'children' => $children,
            'descendants' => $descendants,
            'items' => $items,
            'manager' => $manager,
            'parent' => $parent,
            'rbamParameters' => $rbamParameters,
            'translator' => $translator,
            'type' => $type,
            'urlGenerator' => $urlGenerator,
        ]
    ) ?>
</div>