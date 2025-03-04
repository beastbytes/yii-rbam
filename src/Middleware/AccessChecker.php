<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use StringBackedEnum;
use Yiisoft\Http\Status;
use Yiisoft\User\CurrentUser;

final class AccessChecker implements MiddlewareInterface
{
    private ?string $permission = null;

    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly ResponseFactoryInterface $responseFactory,
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->permission === null) {
            throw new InvalidArgumentException('Permission not set.');
        }

        if (!$this->currentUser->can($this->permission)) {
            return $this
                ->responseFactory
                ->createResponse(Status::FORBIDDEN)
            ;
        }

        return $handler->handle($request);
    }

    public function withPermission(StringBackedEnum|string $permission): self
    {
        $new = clone $this;
        $new->permission = is_string($permission) ? $permission : $permission->value;
        return $new;
    }
}