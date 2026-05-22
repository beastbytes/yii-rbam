<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Middleware;

use BeastBytes\Yii\Rbam\Rbac\Permission;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class AccessChecker implements MiddlewareInterface
{
    private array $arguments = [];
    private ?string $permission = null;
    private array $queryParameters = [];
    private ?string $route = null;
    private ?string $hash = null;

    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->permission === null) {
            throw new InvalidArgumentException('Permission not set');
        }

        if ($this->currentUser->can($this->permission)) {
            return $handler->handle($request);
        }

        $response = $this
            ->responseFactory
            ->createResponse(Status::FORBIDDEN)
        ;

        if ($this->route === null) {
            return $response;
        }

        return $response
            ->withHeader(
                Header::LOCATION,
                $this
                    ->urlGenerator
                    ->generate($this->route, $this->arguments, $this->queryParameters, $this->hash)
            )
        ;
    }

    public function withPermission(Permission|string $permission): self
    {
        $new = clone $this;
        $new->permission = $permission instanceof Permission ? $permission->getItemName() : $permission;
        return $new;
    }

    public function withRoute(
        string $route,
        array $arguments = [],
        array $queryParameters = [],
        ?string $hash = null
    ): self
    {
        $new = clone $this;
        $new->route = $route;
        $new->arguments = $arguments;
        $new->queryParameters = $queryParameters;
        $new->hash = $hash;
        return $new;
    }
}