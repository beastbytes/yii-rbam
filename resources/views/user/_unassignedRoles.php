<?php

declare(strict_types=1);

/**
 * @var Assignment[] $assignments
 * @var Csrf $csrf
 * @var int $currentPage
 * @var Permission[] $permissionsGranted
 * @var Inflector $inflector
 * @var RbamParameters $rbamParameters
 * @var Role[] $roles
 * @var WebView $this
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface $user
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
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
use Yiisoft\Yii\View\Renderer\Csrf;

$this->addJsStrings(['pagination.init("unassigned-roles")']);

$this->setParameter(
    'baseUrl',
    $urlGenerator->generate('rbam.rolesPagination')
);

echo GridView::widget()
    ->dataReader((new OffsetPaginator(new IterableDataReader($roles)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getPageSize())
    )
    ->containerAttributes([
        'class' => 'grid-view roles',
        'id' => 'unassigned-roles',
        'data-_csrf' => $csrf,
        'data-userId' => $user->getId(),
    ])
    ->header($translator->translate('label.roles-unassigned'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n<div class=\"toolbar\">{toolbar}</div>\n{items}")
    ->emptyText($translator->translate('message.no-roles-unassigned'))
    ->urlCreator(function (array $arguments, array $queryParameters): string {
        $baseUrl = $this->getParameter('baseUrl');
        $pathParams = [];

        // Handle path parameters
        foreach ($arguments as $name => $value) {
            $pathParams[] = "$name-$value";
        }

        // Build final URL
        $url = $baseUrl;
        if ($pathParams) {
            $url .= '/' . implode('/', $pathParams);
        }
        if ($queryParameters) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    })
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
;