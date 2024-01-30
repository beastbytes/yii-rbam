<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/**
 * @var Role[] $assignedRoles
 * @var Assignment[] $assignments
 * @var AssetManager $assetManager
 * @var Permission[] $permissionsGranted
 * @var Csrf $csrf
 * @var Inflector $inflector
 * @var Item $item
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var WebView $this
 * @var TranslatorInterface $translator
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
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(RbamAsset::class);
$this->addJsFiles($assetManager->getJsFiles());

$this->setTitle($user->getName());

$breadcrumbs = [
    [
        'label' => $translator->translate('label.rbam'),
        'url' => $urlGenerator->generate('rbam.rbam'),
    ],
    [
        'label' => $translator->translate('label.users'),
        'url' => $urlGenerator->generate('rbam.userIndex'),
    ],
    Html::encode($this->getTitle())
];
$this->setParameter('breadcrumbs', $breadcrumbs);

$assignmentNames = array_keys($assignments);
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= GridView::widget()
    ->dataReader(new IterableDataReader($roles))
    ->containerAttributes(['class' => 'grid_view assignments'])
    ->header($translator->translate('label.roles'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->tbodyAttributes([
        'data-csrf' => $csrf,
        'data-checked_url' => $urlGenerator->generate('rbam.assign'),
        'data-unchecked_url' => $urlGenerator->generate('rbam.revoke'),
        'data-item' => $user->getId(),
        'id' => 'items',
    ])
    ->layout("{header}\n{toolbar}\n{items}")
    ->toolbar(
        Html::div(
            content: Html::button(
                content: $translator->translate($rbamParameters->getButtons('revokeAll')['content']),
                attributes: array_merge(
                    $rbamParameters->getButtons('revokeAll')['attributes'],
                    [
                        'id' => 'all_items',
                        'data-url' => $urlGenerator->generate('rbam.revokeAll'),
                    ]
                )
            ),
            attributes: ['class' => 'toolbar']
        )
            ->render()
    )
    ->emptyText($translator->translate('message.no_assignments_found'))
    ->columns(
        new CheckboxColumn(
            header: $translator->translate('label.assigned'),
            content: static function (Checkbox $input, DataContext $context)
                use ($assignedRoles, $assignmentNames, $inflector)
            {
                $checked = false;
                $disabled = false;

                foreach ($assignedRoles as $assignedRole):
                    if ($context->data === $assignedRole):
                        $checked = true;

                        if (!in_array($context->data->getName(), $assignmentNames, true)) {
                            $disabled = true;
                        }

                        break;
                    endif;
                endforeach;

                return $input
                    ->checked($checked)
                    ->disabled($disabled)
                    ->name($inflector->toSnakeCase($context->data->getName()))
                ;
            }
        ),
        new DataColumn(
            header: $translator->translate('label.role'),
            content: static fn(Item $item) => $item->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.description'),
            content: static fn(Item $item) => $item->getDescription()
        ),
        new DataColumn(
            header: $translator->translate('label.assigned'),
            content: static function (Item $item) use ($assignments, $assignmentNames, $rbamParameters)
            {
                if (in_array($item->getName(), $assignmentNames, true)) {
                    return (new DateTime())
                        ->setTimestamp($assignments[$item->getName()]->getCreatedAt())
                        ->format($rbamParameters->getDatetimeFormat())
                    ;
                }

                return '';
            }
        ),
    )
?>

<?= $this->render(
    '/item/_items',
    [
        'items' => $permissionsGranted,
        'layout' => "{header}\n{items}",
        'toolbar' => '',
        'type' => Item::TYPE_PERMISSION
    ]
) ?>
