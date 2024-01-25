<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Yii\View\ViewRenderer;

use const DIRECTORY_SEPARATOR;

class UserController
{
    public function __construct(
        private FlashInterface $flash,
        private ItemsStorageInterface $itemsStorage,
        private ManagerInterface $manager,
        private UserRepositoryInterface $userRepository,
        private ViewRenderer $viewRenderer
    )
    {
        $this->viewRenderer = $this
            ->viewRenderer
            ->withViewPath('@views/user')
        ;
    }

    public function index(): ResponseInterface
    {
        $users = $this
            ->userRepository
            ->findAll()
        ;

        return $this
            ->viewRenderer
            ->render('index', ['users' => $users])
        ;
    }

    public function assignments(
        CurrentRoute $currentRoute,
        UserRepositoryInterface $userRepository
    ): ResponseInterface
    {
        $userId = $currentRoute
            ->getArgument('id')
        ;

        $user = $userRepository->findById($userId);
        $assignedRoles = $this
            ->manager
            ->getRolesByUserId($userId)
        ;
        $roles = $this
            ->itemsStorage
            ->getRoles()
        ;

        return $this
            ->viewRenderer
            ->render('assignments', ['assignedRoles' => $assignedRoles, 'roles' => $roles, 'user' => $user])
        ;
    }

    public function view(
        CurrentRoute $currentRoute,
        UserRepositoryInterface $userRepository
    ): ResponseInterface
    {
        $userId = $currentRoute
            ->getArgument('id')
        ;

        $user = $userRepository->findById($userId);
        $assignedRoles = $this
            ->manager
            ->getRolesByUserId($userId)
        ;

        return $this
            ->viewRenderer
            ->render('view', ['assignedRoles' => $assignedRoles, 'user' => $user])
        ;
    }
}
