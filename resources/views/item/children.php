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
 * @var int $currentPage
 * @var Item[] $descendants
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item[] $items
 * @var int $pageSize
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
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\View\Renderer\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$this->setTitle($translator->translate(
    $type === Item::TYPE_PERMISSION ? 'label.manage-role-permissions' : 'label.manage-child-roles',
    ['name' => $parent->getName()]
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
                'type' => $inflector->toPlural(ItemTypeService::getItemType($parent))
            ]
        ),
    ],
    [
        'label' => $translator->translate('label.role-name', ['name' => $parent->getName()]),
        'url' => $urlGenerator->generate(
            'rbam.viewItem',
            [
                'name' => $inflector->toSnakeCase($parent->getName()),
                'type' => ItemTypeService::getItemType($parent)
            ]
        ),
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<?= GridView::widget()
    ->dataReader(
        (new OffsetPaginator(new IterableDataReader($items)))
            ->withCurrentPage($currentPage)
            ->withPageSize($pageSize)
    )
    ->containerAttributes(['class' => 'grid-view roles child-roles'])
    ->header($this->getTitle())
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes([
        'data-csrf' => $csrf,
        'data-action' => 'children',
        'data-checked_url' => $urlGenerator->generate('rbam.addChild'),
        'data-unchecked_url' => $urlGenerator->generate('rbam.removeChild'),
        'data-item' => $parent->getName(),
        'id' => 'js-items',
    ])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->toolbar(
        Html::button(
            $translator->translate($rbamParameters->getButtons('removeAll')['content']),
            array_merge(
                $rbamParameters->getButtons('removeAll')['attributes'],
                [
                    'data-url' => $urlGenerator->generate('rbam.removeAll'),
                    'id' => 'all_items',
                    'type' => 'button'
                ]
            )
        )
            ->render()
    )
    ->emptyText($translator->translate('message.no-child_roles'))
    ->columns(
        new CheckboxColumn(
            header: 'Assigned',
            content: static function (
                Checkbox $input,
                DataContext $context
            ) use ($ancestors, $children, $descendants, $inflector) {
                $checked = false;
                $disabled = false;
                $indeterminate = false;

                if (in_array($context->data, $ancestors, true)):
                    $disabled = true;
                else:
                    foreach ($descendants as $descendant):
                        if ($context->data === $descendant):
                            $checked = true;

                            if (!in_array($descendant, $children, true)) {
                                $disabled = true;
                            }
                            break;
                        endif;
                    endforeach;
                endif;

                return $input
                    ->checked($checked)
                    ->disabled($disabled)
                    ->attribute('indeterminate', $indeterminate)
                    ->name($inflector->toSnakeCase($context->data->getName()))
                ;
            }
        ),
        new DataColumn(
            header: $translator->translate('label.' . $type),
            content: static fn(Item $item) => $item->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $item->getDescription()
        ),
        new DataColumn(
            header: $translator->translate('label.created-at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: $translator->translate('label.updated-at'),
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new ActionColumn(
            template: '{view}',
            urlCreator: static function($action, $context) use ($inflector, $urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'Item', [
                    'name' => $inflector->toSnakeCase($context->key),
                    'type' => ItemTypeService::getItemType($context->data)
                ]);
            },
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getButtons('view')['content']),
                    attributes: $rbamParameters->getButtons('view')['attributes'],
                ),
            ],
            bodyAttributes: ['class' => 'action'],
        ),
    )
?>

<?= Html::a(
    $translator->translate($rbamParameters->getButtons('done')['content']),
    $urlGenerator->generate(
        'rbam.viewItem',
        [
            'type' => $type,
            'name' => $inflector->toSnakeCase($parent->getName())
        ]
    ),
    $rbamParameters->getButtons('done')['attributes']
)
    ->render()
?>