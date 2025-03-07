<?php

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Assignment[] $assignments
 * @var ItemsStorageInterface $itemsStorage
 * @var Permission[] $permissionsGranted
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var Role[] $unassignedRoles
 * @var UrlGeneratorInterface $urlGenerator
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\BodyRowContext;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$assignmentNames = array_keys($assignments);

echo GridView::widget()
    ->dataReader(new IterableDataReader($assignedRoles))
    ->containerAttributes(['class' => 'grid-view roles', 'id' => 'assigned-roles'])
    ->header($translator->translate('label.roles-assigned'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->toolbar(
        !empty($assignedRoles)
            ? Html::button(
            $translator->translate($rbamParameters->getButtons('removeAll')['content']),
            array_merge(
                $rbamParameters->getButtons('revokeAll')['attributes'],
                [
                    'data-href' => $urlGenerator->generate('rbam.revokeAllAssignments'),
                    'id' => 'all_items',
                    'type' => 'button',
                ],
            ),
        )
            ->render()
            : ''
    )
    ->emptyText($translator->translate('message.no-roles-assigned'))
    ->bodyRowAttributes(
        static function (Role $role, BodyRowContext $context) use ($assignmentNames)
        {
            return [
                'class' => in_array($role->getName(), $assignmentNames, true)
                    ? 'direct'
                    : 'indirect'
            ];
        }
    )
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Role $role) => $role->getName()

        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Role $role) => $role->getDescription()
        ),
        new DataColumn(
            header: $translator->translate('label.assigned'),
            content: static function (Role $role) use ($assignments, $assignmentNames, $itemsStorage, $rbamParameters)
            {
                if (in_array($role->getName(), $assignmentNames, true)) {
                    return (new DateTime())
                        ->setTimestamp($assignments[$role->getName()]->getCreatedAt())
                        ->format($rbamParameters->getDatetimeFormat())
                    ;
                }

                $ancestors = $itemsStorage
                    ->getParents($role->getName())
                ;

                $parent = array_shift($ancestors);

                return $parent === null ? '' : $parent->getName();
            }
        ),
        new ActionColumn(
            content: static function (Item $item) use (
                $assignmentNames,
                $rbamParameters,
                $translator,
                $urlGenerator
            )
            {
                return Html::button(
                    $translator->translate($rbamParameters->getButtons('revoke')['content']),
                    array_merge(
                        $rbamParameters->getButtons('revoke')['attributes'],
                        [
                            'data-name' => $item->getName(),
                            'data-href' => $urlGenerator->generate('rbam.revokeAssignment'),
                            'disabled' => !in_array($item->getName(), $assignmentNames),
                        ]
                    )
                );
            },
            bodyAttributes: ['class' => 'action'],
        ),
    )
?>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($unassignedRoles))
    ->containerAttributes(['class' => 'grid-view roles', 'id' => 'unassigned-roles'])
    ->header($translator->translate('label.roles-unassigned'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->emptyText($translator->translate('message.no-roles-unassigned'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.name'),
            content: static fn(Role $role) => $role->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Role $role) => $role->getDescription()
        ),
        new ActionColumn(
            content: static function (Item $item) use (
                $rbamParameters,
                $translator,
                $urlGenerator
            )
            {
                return Html::button(
                    $translator->translate($rbamParameters->getButtons('assign')['content']),
                    array_merge(
                        $rbamParameters->getButtons('assign')['attributes'],
                        [
                            'data-name' => $item->getName(),
                            'data-href' => $urlGenerator->generate('rbam.assignRole'),
                        ]
                    )
                );
            },
            bodyAttributes: ['class' => 'action'],
        ),
    )
?>

<?= $this->render(
    '../item/_items',
    [
        'actionButtons' => ['view'],
        'dataReader' => new IterableDataReader($permissionsGranted),
        'header' => $translator->translate('label.permissions-granted'),
        'emptyText' => $translator->translate('message.no-permissions-granted'),
        'itemsStorage' => $itemsStorage,
        'toolbar' => '',
        'translator' => $translator,
        'type' => 'permission',
        'urlGenerator' => $urlGenerator,
    ]
)
?>