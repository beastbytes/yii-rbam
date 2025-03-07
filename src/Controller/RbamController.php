<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class RbamController
{
    public const RBAM_ROLE = 'Rbam';

    public function __construct(
        private readonly FlashInterface $flash,
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

    public function init(
        CurrentUser $currentUser,
        ManagerInterface $manager,
        Redirect $redirect,
    ): ResponseInterface
    {
        if (count($this->itemsStorage->getRoles()) === 0) {
            $now = time();
            $manager
                ->addRole((new Role(self::RBAM_ROLE))
                    ->withDescription($this->translator->translate('title.rbam'))
                    ->withCreatedAt($now)
                    ->withUpdatedAt($now)
                )
            ;
            $manager->assign(self::RBAM_ROLE, $currentUser->getId());

            foreach (RbamPermission::cases() as $permission) {
                $manager
                    ->addPermission((new Permission($permission->name))
                        ->withDescription($this->translator->translate('permission.' . $permission->value))
                        ->withCreatedAt($now)
                        ->withUpdatedAt($now)
                    )
                ;

                $manager->addChild(self::RBAM_ROLE, $permission->name);
            }

            $this
                ->flash
                ->add(
                    'success',
                    $this
                        ->translator
                        ->translate('flash.rbam-initialised')
                )
            ;
        } else {
            $this
                ->flash
                ->add(
                    'info',
                    $this
                        ->translator
                        ->translate('flash.rbam-already-initialised')
                )
            ;
        }

        return $redirect
            ->toRoute('rbam.index')
            ->create()
        ;
    }
}