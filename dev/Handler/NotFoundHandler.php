<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class NotFoundHandler implements RequestHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private CurrentRoute $currentRoute;
    private ViewRenderer $viewRenderer;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        CurrentRoute $currentRoute,
        ViewRenderer $viewRenderer
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->currentRoute = $currentRoute;
        $this->viewRenderer = $viewRenderer->withControllerName('country');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewRenderer
            ->render('404', ['urlGenerator' => $this->urlGenerator, 'currentRoute' => $this->currentRoute])
            ->withStatus(Status::NOT_FOUND);
    }
}
