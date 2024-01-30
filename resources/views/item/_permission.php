<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Role[] $ancestors
 * @var AssignmentsStorageInterface $assignmentsStorage
 * @var Permission $item
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\ListView;

echo GridView::widget()
    ->dataReader(new IterableDataReader($users))
    ->containerAttributes(['class' => 'grid_view permitted_users'])
    ->header($translator->translate('label.permitted_users'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{items}")
    ->emptyText($translator->translate('message.no_users_found'))
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
            ]
        )
    )
;
