<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var array $permissions
 * @var array $roles
 * @var Translator $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var array $users
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

echo GridView::widget()
    ->dataReader(new IterableDataReader($roles))
    ->tableAttributes(['class' => 'grid_view child_roles'])
    ->layout("{header}\n{toolbar}\n{items}")
    ->header($translator->translate('title.child_roles'))
    ->toolbar(
        Html::div(
            content: Html::a(
                content: $translator->translate('button.manage_child_roles'),
                url: $urlGenerator->generate(
                    'rbam.children',
                    ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_ROLE]
                ),
                attributes: ['class' => ['button', 'add']]
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_child_roles'))
    ->columns(
        new DataColumn(
            header: ucfirst(Item::TYPE_ROLE),
            content: static function ($item) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $item->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewItem',
                        ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_ROLE]
                    )
                )
                ->render();
            }
        ),
        new DataColumn(header: 'Description', content: static fn(Item $item) => $item->getDescription())
    )
;

echo GridView::widget()
    ->dataReader(new IterableDataReader($permissions))
    ->tableAttributes(['class' => 'grid_view permissions_granted'])
    ->layout("{header}\n{toolbar}\n{items}")
    ->header($translator->translate('title.permissions_granted'))
    ->toolbar(
        Html::div(
            content: Html::a(
                content: $translator->translate('button.manage_permissions'),
                url: $urlGenerator->generate(
                    'rbam.children',
                    ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_PERMISSION]
                ),
                attributes: ['class' => ['button', 'add']]
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_permissions_granted'))
    ->columns(
        new DataColumn(
            header: ucfirst(Item::TYPE_PERMISSION),
            content: static function ($item) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $item->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewItem',
                        ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_PERMISSION]
                    )
                )
                ->render();
            }
        ),
        new DataColumn(header: 'Description', content: static fn(Item $item) => $item->getDescription())
    )
;

echo ListView::widget()
    ->dataReader(new IterableDataReader($users))
    ->webView($this)
    ->containerAttributes(['class' => 'list_view assigned_users'])
    ->header($translator->translate('title.assigned_users'))
    ->emptyText($translator->translate('message.no_users_assigned'))
    ->itemView(static function ($user) use ($urlGenerator) {
        return Html::a(
            content: $user->getName(),
            url: $urlGenerator->generate(
                'rbam.viewUser',
                ['id' => $user->getId()]
            )
        )
        ->render();
    })
;
