<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Strings\Inflector;

final class AssignmentController
{
    public function __construct(
        private readonly DataResponseFactoryInterface $responseFactory,
        private readonly Inflector $inflector,
        private readonly ManagerInterface $manager,
    )
    {}

    public function assign(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->assign(
                $this
                    ->inflector
                    ->toPascalCase($parsedBody['name']),
                $parsedBody['item']
            )
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }

    public function revoke(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->revoke(
                $this
                    ->inflector
                    ->toPascalCase($parsedBody['name']),
                $parsedBody['item']
            )
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }

    public function revokeAll(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $this
            ->manager
            ->revokeAll($parsedBody['item'])
        ;

        return $this
            ->responseFactory
            ->createResponse()
        ;
    }
}
