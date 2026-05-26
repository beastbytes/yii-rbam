<?php

declare(strict_types=1);

/**
 * @var Item[] $children
 * @var string $childType
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item[] $orphans
 * @var Item $parent
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var string $type
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->registerJs('const rbam = new Rbam("manage-children");');

$this->setTitle($translator->translate(
    id: $type === Item::TYPE_PERMISSION
        ? 'label.child-permissions.manage'
        : ($childType === Item::TYPE_PERMISSION
            ? 'label.permissions.manage'
            : 'label.child-roles.manage'
        )
    ,
    category: 'rbam'
));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate(id: sprintf('label.%ss', $type), category: 'rbam'),
        'url' => $urlGenerator->generate(
            'rbam.item.index',
            [
                'type' => $type . 's',
            ],
        ),
    ],
    [
        'label' => $parent->getName(),
        'url' => $urlGenerator->generate(
            'rbam.item.view',
            [
                'name' => $parent->getName(),
                'type' => $parent->getType(),
            ],
        ),
    ],
    $this->getTitle(),
];

$this->setParameter('breadcrumbs', $breadcrumbs);

echo Html::div()
    ->attributes([
        'data-_csrf' => $csrf,
        'id' => 'manage-children',
    ])
    ->open()
;

echo Html::h2($translator->translate(
    $type === Item::TYPE_PERMISSION
        ? 'label.child-permissions.manage-for'
        : ($childType === Item::TYPE_PERMISSION
            ? 'label.permissions.manage-for'
            : 'label.child-roles.manage-for'
        )
    ,
    ['parent' => $parent->getName()],
    'rbam'
));

echo $this->render(
    '_children',
    [
        'children' => $children,
        'childType' => $childType,
        'parent' => $parent,
        'type' => $type,
    ]
);

echo $this->render(
    '_orphans',
    [
        'childType' => $childType,
        'orphans' => $orphans,
        'parent' => $parent,
        'type' => $type,
    ]
);

echo Html::div()->close();