<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class RbamController
{
    public const RBAM_ROLE = 'Rbam';

    public function __construct(
        private readonly ItemsStorageInterface $itemsStorage,
        private TranslatorInterface $translator,
        private ViewRenderer $viewRenderer
    )
    {
        $this->translator = $this
            ->translator
            ->withDefaultCategory('rbam')
        ;
        $this->viewRenderer = $this
            ->viewRenderer
            ->withController($this)
        ;
    }

    #[PermissionAttribute(
        name: RbamPermission::RbamIndex,
        description: 'Allows access to RBAM',
        parent: self::RBAM_ROLE
    )]
    public function index(
        RuleServiceInterface $ruleService,
        UserRepositoryInterface $userRepository,
    ): ResponseInterface
    {
        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'roles' => $this
                        ->itemsStorage
                        ->getRoles()
                    ,
                    'permissions' => $this
                        ->itemsStorage
                        ->getPermissions()
                    ,
                    'rules' => $ruleService->getRules(),
                    'users' => $userRepository->findAll()
                ]
            )
        ;
    }
}