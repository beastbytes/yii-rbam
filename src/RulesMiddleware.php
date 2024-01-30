<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Rbac\RuleFactoryInterface;

final class RulesMiddleware implements MiddlewareInterface
{
    private array $rules = [];

    public function __construct(private readonly RuleFactoryInterface $container)
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->rules as $rule) {
            $this->container->create($rule);
        }

        return $handler->handle($request);
    }

    public function rules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }
}
