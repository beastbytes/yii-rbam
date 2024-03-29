<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
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
            ]
        )
    )
;

echo $this->render(
    '_items',
    [
        'actionButtons' => ['view'],
        'dataReader' => new IterableDataReader($roles),
        'layout' => "{header}\n{toolbar}\n{items}",
        'toolbar' => Html::a(
            content: $translator->translate($rbamParameters->getButtons('manageChildRoles')['content']),
            url: $urlGenerator->generate(
                'rbam.children',
                ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_ROLE]
            ),
            attributes: $rbamParameters->getButtons('manageChildRoles')['attributes']
        ),
        'translator' => $translator,
        'type' => Item::TYPE_ROLE,
        'urlGenerator' => $urlGenerator,
    ]
);

echo $this->render(
    '_items',
    [
        'actionButtons' => ['view'],
        'dataReader' => new IterableDataReader($permissions),
        'layout' => "{header}\n{toolbar}\n{items}",
        'toolbar' => Html::a(
            content: $translator->translate($rbamParameters->getButtons('managePermissions')['content']),
            url: $urlGenerator->generate(
                'rbam.children',
                ['name' => $inflector->toSnakeCase($item->getName()), 'type' => Item::TYPE_PERMISSION]
            ),
            attributes: $rbamParameters->getButtons('managePermissions')['attributes']
        ),
        'translator' => $translator,
        'type' => Item::TYPE_PERMISSION,
        'urlGenerator' => $urlGenerator,
    ]
);
