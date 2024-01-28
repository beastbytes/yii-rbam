<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Assignment[] $assignments
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var Permission[] $permissions
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var TranslatorInterface $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

echo GridView::widget()
    ->dataReader(new IterableDataReader($users))
    ->containerAttributes(['class' => 'grid_view assignments'])
    ->header($translator->translate('label.assignments'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{items}")
    ->emptyText($translator->translate('message.no_assignments_found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.user'),
            content: static fn (UserInterface $user) => $user->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.assigned'),
            content: static function (UserInterface $user) use ($assignments, $rbamParameters) {
                $userId = $user->getId();

                foreach ($assignments as $assignment) {
                    if ($userId === $assignment->getUserId()) {
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
            urlCreator: static function($action, $context) use ($inflector, $urlGenerator)
            {
                return $urlGenerator->generate('rbam.' . $action . 'User', [
                    'id' => $context->data->getid()
                ]);
            },
            buttons: [
                'view' => new ActionButton(
                    content: $translator->translate($rbamParameters->getActionButton('view')['content']),
                    attributes: $rbamParameters->getActionButton('view')['attributes'],
                ),
            ]
        )
    )
;

echo $this->render(
    '_items',
    [
        'items' => $roles,
        'layout' => "{header}\n{toolbar}\n{items}",
        'toolbar' => Html::a(
            content: $translator->translate('button.manage_child_roles'),
            url: $urlGenerator->generate(
                'rbam.children',
                ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_ROLE]
            ),
            attributes: ['class' => ['button', 'manage']]
        ),
        'translator' => $translator,
        'type' => Item::TYPE_ROLE,
        'urlGenerator' => $urlGenerator,
    ]
);

echo $this->render(
    '_items',
    [
        'items' => $permissions,
        'layout' => "{header}\n{toolbar}\n{items}",
        'toolbar' => Html::a(
            content: $translator->translate('button.manage_permissions'),
            url: $urlGenerator->generate(
                'rbam.children',
                ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_PERMISSION]
            ),
            attributes: ['class' => ['button', 'manage']]
        ),
        'translator' => $translator,
        'type' => Item::TYPE_PERMISSION,
        'urlGenerator' => $urlGenerator,
    ]
);
