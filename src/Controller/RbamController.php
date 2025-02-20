<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

class RbamController
{
    public function __construct(
        private ViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this)
        ;
    }

    public function index(
        ItemsStorageInterface $itemsStorage,
        RuleServiceInterface $ruleService,
        UserRepositoryInterface $userRepository
    ): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'roles' => $itemsStorage->getRoles(),
                    'permissions' => $itemsStorage->getPermissions(),
                    'rules' => $ruleService->getRules(),
                    'users' => $userRepository->findAll()
                ]
            )
        ;
    }
}