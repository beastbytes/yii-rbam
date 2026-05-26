<?php

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var int $currentPage
 * @var CurrentUser $currentUser
 * @var Inflector $inflector
 * @var array $items
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var string $type
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate(id: 'label.' . $type . 's'));
$this->registerJs(sprintf('rbam = new Rbam("%s")', $type));

$breadcrumbs = [
    [
        'label' => $translator->translate(id: 'label.rbam', category: 'rbam'),
        'url' => $urlGenerator->generate('rbam.rbam')
    ],
    $this->getTitle()
];
$this->setParameter('breadcrumbs', $breadcrumbs);

echo $this->render(
'_items',
    [
        'actionButtons' => ['view', 'update', 'remove'],
        'currentPage' => $currentPage,
        'currentUser' => $currentUser,
        'noResultsText' => sprintf('message.%s.none-found', $type),
        'header' => $this->getTitle(),
        'item' => null,
        'items' => $items,
        'paginationUrl' => $urlGenerator->generate('rbam.item.index', ['type' => $type . 's']),
        'toolbar' => $currentUser->can(RbamPermission::itemCreate->getItemName())
            ? Html::div(Html::a(
                content: $translator->translate(
                    id: $rbamParameters->getButtons('create' . ucfirst($type))['content'],
                    category: 'rbam'
                ),
                url: $urlGenerator->generate('rbam.item.create', ['type' => $type]),
                attributes: $rbamParameters->getButtons('create' . ucfirst($type))['attributes']
            ))
            : ''
        ,
        'translator' => $translator,
        'type' => $type,
        'urlGenerator' => $urlGenerator,
        'user' => null,
    ]
);