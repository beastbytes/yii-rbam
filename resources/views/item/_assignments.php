<?php

declare(strict_types=1);

/**
 * @var Assignment[] $assignments
 * @var ?int $currentPage
 * @var Inflector $inflector
 * @var Item $item
 * @var ItemsStorageInterface $itemsStorage
 * @var RbamParameters $rbamParameters
 * @var TranslatorInterface $translator
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var UserInterface[] $users
 */

use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\UserInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Column\ActionButton;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->addJsStrings(['pagination.init("assignments")']);

if ($item !== null) {
    $this->setParameter(
        'baseUrl',
        $urlGenerator->generate('rbam.assignmentPagination')
    );
}

echo GridView::widget()
    ->dataReader((new OffsetPaginator(new IterableDataReader($users)))
        ->withCurrentPage($currentPage ?? 1)
        ->withPageSize($rbamParameters->getTabPageSize())
    )
    ->containerAttributes([
        'class' => 'grid-view assignments',
        'id' => 'assignments',
        'data-name' => $item->getName(),
    ])
    ->header($translator->translate('label.assignments'))
    ->headerAttributes(['class' => 'header'])
    ->tableAttributes(['class' => 'grid'])
    ->layout("{header}\n{items}")
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
    ->emptyText($translator->translate('message.no-assignments-found'))
    ->columns(
        new DataColumn(
            header: $translator->translate('label.user'),
            content: static fn (UserInterface $user) => $user->getName()
        ),
        new DataColumn(
            header: $translator->translate('label.assigned'),
            content: static function (UserInterface $user) use (
                $assignments,
                $item,
                $itemsStorage,
                $rbamParameters,
                $translator
            ) {
                $userId = $user->getId();

                foreach ($assignments as $assignment) {
                    if ($userId === $assignment->getUserId()) {
                        return (new DateTime())
                            ->setTimestamp($assignment->getCreatedAt())
                            ->format($rbamParameters->getDatetimeFormat())
                        ;
                    }
                }

                $ancestors = $itemsStorage
                    ->getParents($item->getName())
                ;

                $parent = array_shift($ancestors);

                return $parent->getName();
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
;
