<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\Translator;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle(
    $translator->translate('title.user_assignments', ['name' => $user->getName()])
);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.users'),
        'url' => $urlGenerator->generate('rbam.userIndex'),
    ],
    $user->getName()
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($assignedRoles))
    ->attributes(['class' => 'grid_view users'])
    ->layout('')
    ->layoutGridTable("{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::a(
                content: $translator->translate('button.manage_role_assignments'),
                url: $urlGenerator->generate('rbam.userAssignments', ['id' => $user->getId()]),
                attributes: $rbamParameters->getActionButton('manageRoleAssignments')['attributes']
           )
        )
        ->render()
    )
    ->emptyText(
        $translator->translate('message.no_assignments_found')
    )
    ->columns(
        new DataColumn(
            header: ucfirst(Item::TYPE_ROLE),
            content: static function (Item $item) use ($inflector, $urlGenerator) {
                return Html::a(
                    content: $item->getName(),
                    url: $urlGenerator->generate(
                        'rbam.viewItem',
                        ['name' => $inflector->toSnakeCase($item->getName()), 'type' => $item->getType()]
                    )
                )
                ->render();
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
