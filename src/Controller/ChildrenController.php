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

final class ChildrenController
{
    public function __construct(
        private DataResponseFactoryInterface $responseFactory,
        private Inflector $inflector,
        private ManagerInterface $manager,
    )
    {}

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
