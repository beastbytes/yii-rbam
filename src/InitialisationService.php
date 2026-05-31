<?php

namespace BeastBytes\Yii\Rbam;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Item as ItemAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Permission as RbamPermission;
use ReflectionClass;
use ReflectionMethod;
use Yiisoft\Files\FileHelper;
use Yiisoft\Rbac\Exception\ItemAlreadyExistsException;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;

final class InitialisationService implements InitialisationServiceInterface
{
    /** @var list<string> $errors */
    private array $errors = [];

    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly TranslatorInterface $translator,
    )
    {}

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function processFile(string $file): void
    {
        $namespace = '';
        $stream = FileHelper::openFile($file, 'r+');

        do {
            $line = fgets($stream);
            if (str_starts_with($line, 'namespace')) {
                $namespace = substr($line, 10, -2);
            }
        } while (empty($namespace));

        fclose($stream);

        $class = substr($file, strrpos($file, '/') + 1, -4);

        $className = $namespace . '\\' . $class;

        if (class_exists($className)) {
            $this->processClass(new ReflectionClass($className));
        }
    }

    private function processClass(ReflectionClass $reflectionClass): void
    {
        $permissions = [];

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $permission = $this->processMethod($reflectionClass, $reflectionMethod);

            if ($permission) {
                $permissions[] = $permission;
            }
        }

        /** @var ItemAttribute $attribute */
        foreach ($reflectionClass->getAttributes(RoleAttribute::class) as $attribute) {
            $attribute = $attribute->newInstance();

            $name = $attribute->getName();

            try {
                $this->manager->addRole(new Role($name));
            } catch (ItemAlreadyExistsException) {} // Don't do anything

            $this
                ->manager
                ->updateRole(
                    $name,
                    $this
                        ->manager
                        ->getRole($name)
                        ->withDescription($attribute->getDescription())
                        ->withRuleName($attribute->getRuleName())
                )
            ;

            $this->processParents($attribute);

            foreach ($permissions as $permission) {
                if ($this->manager->canAddChild($name, $permission)) {
                    $this->manager->addChild($name, $permission);
                }
            }
        }
    }

    private function processMethod(ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod): ?string
    {
        $attributes = $reflectionMethod->getAttributes(PermissionAttribute::class);

        if (count($attributes) !== 1) {
            if (count($attributes) > 1) {
                $this->errors[] = ($this->translator->translate(
                    'rbac.error.permission-count',
                    ['class' => $reflectionClass->getName(), 'method' => $reflectionMethod->getName()]
                ));

            }
            return null;
        }

        /** @var ItemAttribute $attribute */
        $attribute = $attributes[0]->newInstance();
        $name = $attribute->getName();

        try {
            $this->manager->addPermission(new Permission($name));
        } catch (ItemAlreadyExistsException) {} // Don't do anything

        $this
            ->manager
            ->updatePermission(
                $name,
                $this
                    ->manager
                    ->getPermission($name)
                    ->withDescription($attribute->getDescription())
                    ->withRuleName($attribute->getRuleName())
            )
        ;

        $this->processParents($attribute);

        return $name;
    }


    /**
     * Add the item as a child to any parent items
     * If the parents already exist, that's OK.
     * If not, they are creates and will be updated as files are processed
     *
     * @param ItemAttribute $item
     */
    private function processParents(ItemAttribute $item): void
    {
        foreach ($item->getParents() as $parent) {
            if ($parent instanceof RbamPermission) {
                try {
                    $this->manager->addPermission(new Permission($parent->getItemName()));
                } catch (ItemAlreadyExistsException) {} // Don't do anything
            } else {
                try {
                    $this->manager->addRole(new Role($parent->getItemName()));
                } catch (ItemAlreadyExistsException) {} // Don't do anything
            }

            if ($this->manager->canAddChild($parent->getItemName(), $item->getName())) {
                $this->manager->addChild($parent->getItemName(), $item->getName());
            }
        }

    }
}