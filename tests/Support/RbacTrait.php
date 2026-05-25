<?php

namespace Tests\Support;

use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Php\AssignmentsStorage;
use Yiisoft\Rbac\Php\ItemsStorage;
use Yiisoft\Rbac\Role;

trait RbacTrait
{
    private static ?AssignmentsStorageInterface $assignmentsStorage  = null;
    private static ?ItemsStorageInterface $itemsStorage = null;

    private static array $rbam = [
        'assignments' => [
            'rbam.admin' => [
                self::CURRENT_USER,
            ],
        ],
        'children' => [
            'rbam.admin' => [
                'rbam.clear',
                'rbam.index',
                'rbam.item.manager',
                'rbam.rule.manager',
                'rbam.user.manager',
            ],
            'rbam.item.manager' => [
                'rbam.index',
                'rbam.item.create',
                'rbam.item.remove',
                'rbam.item.update',
                'rbam.item.view',
            ],
            'rbam.rule.manager' => [
                'rbam.index',
                'rbam.rule.create',
                'rbam.rule.delete',
                'rbam.rule.update',
                'rbam.rule.view',
            ],
            'rbam.user.manager' => [
                'rbam.index',
                'rbam.user.update',
                'rbam.user.view',
            ],
        ],
    ];

    protected function applyRule(?string $rule, string $name): void
    {
        $item = self::getRbacManager()->getPermission($name) ?? self::getRbacManager()->getRole($name);

        $item = $item->withRuleName(is_string($rule)
            ? 'BeastBytes\\Yii\\Rbam\\Rbac\\Rule\\' . $rule . 'Rule'
            : $rule
        );

        $item instanceof Permission
            ? self::getRbacManager()->updatePermission($name, $item)
            : self::getRbacManager()->updateRole($name, $item)
        ;

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'items/php',
                force: true)
            ;
        }
    }

    protected function getPermission(string $name): Permission
    {
        return self::getRbacManager()->getPermission($name);
    }

    public static function initRbac(): void
    {
        $rbacManager = self::getRbacManager();

        foreach (RbamPermission::cases() as $item) {
            $rbacManager->addPermission((new Permission($item->getItemName()))
                ->withDescription($item->getDescription())
            );
        }

        foreach (RbamRole::cases() as $item) {
            $rbacManager->addRole((new Role($item->getItemName()))
                ->withDescription($item->getDescription())
            );
        }

        foreach (self::$rbam['children'] as $parent => $children) {
            foreach ($children as $child) {
                $rbacManager->addChild($parent, $child);
            }
        }

        foreach (self::$rbam['assignments'] as $role => $users) {
            foreach ($users as $user) {
                $rbacManager->assign($role, $user);
            }
        }
    }

    public static function clearRbac(): void
    {
        self::getAssignmentsStorage()->clear();
        self::getItemsStorage()->clear();
    }

    protected static function getRbacManager(): ManagerInterface
    {
        return new Manager(self::getItemsStorage(), self::getAssignmentsStorage());
    }

    protected static function getAssignmentsStorage(): AssignmentsStorageInterface
    {
        if (self::$assignmentsStorage === null) {
            self::$assignmentsStorage = new AssignmentsStorage(__DIR__ . DIRECTORY_SEPARATOR . 'Rbac' . DIRECTORY_SEPARATOR . 'assignments.php');
        }

        return self::$assignmentsStorage;
    }

    protected static function getItemsStorage(): ItemsStorageInterface
    {
        if (self::$itemsStorage === null) {
            self::$itemsStorage = new ItemsStorage(__DIR__ . DIRECTORY_SEPARATOR . 'Rbac' . DIRECTORY_SEPARATOR . 'items.php');
        }

        return self::$itemsStorage;
    }
}