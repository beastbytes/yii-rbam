<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\Dev\User\UserRepository;
use BeastBytes\Yii\Rbam\RbamParameters;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Yii\View\ViewRenderer;

class RbamController
{
    public function __construct(
        private ViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withViewPath('@views/rbam')
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
