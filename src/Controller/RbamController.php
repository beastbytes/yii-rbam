<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Rbam\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class RbamController
{
    private const RBAM_ROLE = 'rbam.admin';

    public function __construct(
        private readonly ItemsStorageInterface $itemsStorage,
        private TranslatorInterface $translator,
        private WebViewRenderer $viewRenderer
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

    /**
     * Display the RBAM dashboard
     *
     * @param RuleServiceInterface $ruleService
     * @param UserRepositoryInterface $userRepository
     * @return ResponseInterface
     */
    #[PermissionAttribute(
        name: RbamPermission::index,
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