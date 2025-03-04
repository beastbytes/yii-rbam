<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Strings\Inflector;

final class ChildrenController
{
    public function __construct(
        private readonly DataResponseFactoryInterface $responseFactory,
        private readonly Inflector $inflector,
        private readonly ManagerInterface $manager,
    )
    {}

    #[PermissionAttribute(
        name: RbamPermission::ItemUpdate,
        description: 'Update a RBAC Item',
        parent: RbamController::RBAM_ROLE
    )]
    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->addChild(
                $parsedBody['item'],
                $this
                    ->inflector
                    ->toPascalCase($parsedBody['name'])
            )
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::ItemUpdate,
        description: 'Update a RBAC Item',
        parent: RbamController::RBAM_ROLE
    )]
    public function remove(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->removeChild(
                $parsedBody['item'],
                $this
                    ->inflector
                    ->toPascalCase($parsedBody['name'])
            )
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::ItemUpdate,
        description: 'Update a RBAC Item',
        parent: RbamController::RBAM_ROLE
    )]
    public function removeAll(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->removeChildren($parsedBody['item'])
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }
}