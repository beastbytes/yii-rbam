<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Role[] $ancestors
 * @var AssignmentsStorageInterface $assignmentsStorage
 * @var int $currentPage
 * @var Inflector $inflector
 * @var Permission $item
 * @var ItemsStorageInterface $itemsStorage
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\MermaidHierarchyDiagram;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Widgets\Assets\TabsAsset;
use BeastBytes\Yii\Widgets\Tabs;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$assetManager->register(TabsAsset::class);
$this->addJsStrings(['pagination.init("permitted-users")']);

echo Tabs::widget(['data' => [
    $translator->translate('label.permitted-users') => GridView::widget()
        ->dataReader((new OffsetPaginator(new IterableDataReader($users)))
            ->withCurrentPage($currentPage ?? 1)
            ->withPageSize($rbamParameters->getTabPageSize())
        )
        ->containerAttributes(['class' => 'grid-view permitted-users', 'id' => 'permitted-users'])
        ->header($translator->translate('label.permitted-users'))
        ->headerAttributes(['class' => 'header'])
        ->tableAttributes(['class' => 'grid'])
        ->layout("{header}\n{items}")
        ->emptyText($translator->translate('message.no-users-found'))
        ->columns(
            new DataColumn(
                header: $translator->translate('label.user'),
                content: static fn (UserInterface $user) => $user->getName()
            ),
            new DataColumn(
                header: $translator->translate('label.role'),
                content: static function (UserInterface $user) use ($ancestors, $assignmentsStorage, $rbamParameters) {
                    $userId = $user->getId();

                    foreach ($ancestors as $ancestor) {
                        $assignment = $assignmentsStorage->get($ancestor->getName(), $userId);
                        if ($assignment !== null) {
                            return $ancestor->getName();
                        }
                    }

                    return '';
                }
            ),
            new DataColumn(
                header: $translator->translate('label.assigned'),
                content: static function (UserInterface $user) use ($ancestors, $assignmentsStorage, $rbamParameters) {
                    $userId = $user->getId();

                    foreach ($ancestors as $ancestor) {
                        $assignment = $assignmentsStorage->get($ancestor->getName(), $userId);
                        if ($assignment !== null) {
                            return (new DateTime())
                                ->setTimestamp($assignment->getCreatedAt())
                                ->format($rbamParameters->getDatetimeFormat())
                            ;
                        }
                    }

                    return '';
                }
            ),
            new ActionColumn(
                template: '{view}',
                urlCreator: static function($action, $context) use ($urlGenerator)
                {
                    return $urlGenerator->generate('rbam.' . $action . 'User', [
                        'id' => $context->data->getid()
                    ]);
                },
                buttons: [
                    'view' => new ActionButton(
                        content: $translator->translate($rbamParameters->getButtons('view')['content']),
                        attributes: $rbamParameters->getButtons('view')['attributes'],
                    ),
                ],
                bodyAttributes: ['class' => 'action'],
            )
        )
        ->render()
    ,
    $translator->translate('label.diagram') => (new MermaidHierarchyDiagram(
        $item,
        $itemsStorage,
        $inflector,
        $translator,
        $urlGenerator)
    )->render(),
]]);