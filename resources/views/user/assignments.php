<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var AssetManager $assetManager
 * @var Role[] $assignedRoles
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var WebView $this
 * @var Translator $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\Assets\RbamAsset;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Role;
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

$title = $translator->translate('title.manage_role_assignments', ['name' => $user->getName()]);
$this->setTitle($title);

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.user_index'),
        'url' => $urlGenerator->generate('rbam.userIndex'),
    ],
    [
        'label' => $translator->translate('label.user_view', ['name' => $user->getName()]),
        'url' => $urlGenerator->generate('rbam.viewUser', ['id' => $user->getId()]),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($roles))
    ->attributes(['class' => 'grid_view roles assigned_roles'])
    ->tbodyAttributes([
        'data-csrf' => $csrf,
        'data-checked_url' => $urlGenerator->generate('rbam.assign'),
        'data-unchecked_url' => $urlGenerator->generate('rbam.revoke'),
        'data-item' => $user->getId(),
        'id' => 'items',
    ])
    ->layout('')
    ->layoutGridTable("{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::button(
                content: $translator->translate('button.revoke_all'),
                attributes: [
                    'class' => 'btn',
                    'id' => 'all_items',
                    'data-url' => $urlGenerator->generate('rbam.revokeAll'),
                ]
            ),
            attributes: ['class' => 'toolbar']
        )
        ->render()
    )
    ->emptyText($translator->translate('message.no_assignments_found'))
    ->columns(
        new CheckboxColumn(
            header: 'Assigned',
            content: static function (Checkbox $input, DataContext $context) use ($assignedRoles, $inflector) {
                $checked = false;

                foreach ($assignedRoles as $assignedRole):
                    if ($context->data === $assignedRole):
                        $checked = true;
                        break;
                    endif;
                endforeach;

                return $input
                    ->checked($checked)
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
