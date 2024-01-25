<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Item[] $children
 * @var Item[] $descendants
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item[] $items
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$this->setTitle(
    $translator->translate('title.manage_child_roles', ['name' => $parent->getName()])
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.roles'),
        'url' => $urlGenerator->generate('rbam.itemIndex', ['type' => $inflector->toPlural($parent->getType())]),
    ],
    [
        'label' => $translator->translate('label.role', ['name' => $parent->getName()]),
        'url' => $urlGenerator->generate(
            'rbam.viewItem',
            [
                'name' => $inflector->toSnakeCase($parent->getName()),
                'type' => $parent->getType()
            ]
        ),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($items))
    ->tableAttributes(['class' => 'grid_view roles child_roles'])
    ->tbodyAttributes([
        'data-csrf' => $csrf,
        'data-action' => 'children',
        'data-checked_url' => $urlGenerator->generate('rbam.addChild'),
        'data-unchecked_url' => $urlGenerator->generate('rbam.removeChild'),
        'data-item' => $parent->getName(),
        'id' => 'items',
    ])
    ->layout("{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::button(
                 content: $translator->translate('button.remove_all'),
                 attributes: [
                     'class' => 'btn',
                     'data-url' => $urlGenerator->generate('rbam.removeAll'),
                     'id' => 'all_items',
                ]
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_child_roles'))
    ->columns(
        new CheckboxColumn(
            header: 'Assigned',
            content: static function (
                Checkbox $input,
                DataContext $context
            ) use ($parent, $children, $descendants, $inflector) {
                $checked = false;
                $disabled = false;
                $indeterminate = false;

                if ($context->data === $parent) {
                    $disabled = $indeterminate = true;
                } else {
                    foreach ($descendants as $descendant):
                        if ($context->data === $descendant):
                            $checked = true;

                            foreach ($children as $child):
                                if ($child === $descendant):
                                    break;
                                endif;
                                $disabled = true;
                            endforeach;

                            break;
                        endif;
                    endforeach;
                }

                return $input
                    ->checked($checked)
                    ->disabled($disabled)
                    ->attribute('indeterminate', $indeterminate)
                    ->name($inflector->toSnakeCase($context->data->getName()))
                ;
            }
        ),
        new DataColumn(
            header: ucfirst(Item::TYPE_ROLE),
            content: static function (Item $item) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $item->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewItem',
                        ['name' => $inflector->toSnakeCase($item->getName()), 'type' => $item->getType()]
                    )
                );
            }
        ),
        new DataColumn(header: 'Description', content: static fn(Item $item) => $item->getDescription()),
        new DataColumn(
            header: 'Created',
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getCreatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        ),
        new DataColumn(
            header: 'Updated',
            content: static fn(Item $item) => (new DateTime())
                ->setTimestamp($item->getUpdatedAt())
                ->format($rbamParameters->getDatetimeFormat())
        )
    )
?>


<?php /*
array_walk($items, static function(&$item) use ($parent, $children, $descendants, $inflector) {
    $checked = false;
    $disabled = false;
    $indeterminate = false;

    if ($item === $parent) {
        $disabled = $indeterminate = true;
    } else {
        foreach ($descendants as $descendant):
            if ($item === $descendant):
                $checked = true;

                foreach ($children as $child):
                    if ($child === $descendant):
                        break;
                    endif;
                    $disabled = true;
                endforeach;

                break;
            endif;
        endforeach;
    }

    $content = Checkbox::tag()
        ->checked($checked)
        ->disabled($disabled)
        ->attribute('indeterminate', $indeterminate)
        ->name($inflector->toSnakeCase($item->getName()))
    ;
    $content .= Span::tag()
        ->content($item->getName())
        ->attributes(['class' => 'key'])
    ;
    $content .= Span::tag()
        ->content($item->getDescription())
        ->attributes(['class' => 'value'])
    ;
    $item = Li::tag()
        ->content($content)
        ->encode(false)
    ;
});

/** @var Li[] $items *
echo Ol::tag()
       ->items(...$items)
       ->attributes([
           'data-csrf' => $csrf,
           'data-action' => 'children',
           'data-checked_url' => $urlGenerator->generate('rbam.addChild'),
           'data-unchecked_url' => $urlGenerator->generate('rbam.removeChild'),
           'data-item' => $parent->getName(),
           'id' => 'items',
       ])
       ->render()
;

echo Button::tag()
    ->attributes([
        'class' => 'btn',
        'data-url' => $urlGenerator->generate('rbam.removeAll'),
        'id' => 'all_items',
    ])
    ->content($translator->translate('button.remove_all'))
    ->render()
;
*/
