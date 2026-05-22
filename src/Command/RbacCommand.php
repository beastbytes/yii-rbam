<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command;

use BeastBytes\Yii\Rbam\Rbac\Attribute\Item as ItemAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Files\FileHelper;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Rbac\Exception\ItemAlreadyExistsException;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;

#[AsCommand(
    name: 'rbac:initialise',
    description: 'Initialise RBAC Permissions and Roles',
    help: 'Scans the project files and creates RBAC items and hierarchy from attributes'
)]
final class RbacCommand extends Command
{
    private const string ONLY_CONTROLLERS = '**Controller.php';

    /** @var list<string> $errors */
    private array $errors = [];
    private SymfonyStyle $io;

    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly TranslatorInterface $translator,
    )
    {
        parent::__construct();
    }

    public function __invoke(
        SymfonyStyle $io,

        #[Argument(description: 'Path for source files (default is current working directory)')]
        ?string $src = null,

        #[Option(description: 'Exclude path from source files', name: 'except', shortcut: 'E')]
        array $except = ['./config/**', './resources/**', './tests/**', './vendor/**'],

        #[Option(description: 'Use Only the specified pattern for matching source files', name: 'only', shortcut: 'O')]
        array $only = [self::ONLY_CONTROLLERS],
    ): int
    {
        $io->title($this->translator->translate('rbac.title'));
        $userId = $io->ask($this->translator->translate('rbac.rbam-admin'), '1');

        $files = array_merge(
            FileHelper::findFiles(
                $src ?? getcwd(),
                [
                    'filter' => (new PathMatcher())
                        ->only(...$only)
                        ->except(...$except),
                    'recursive' => true,
                ]
            ),
            FileHelper::findFiles(
                dirname(__DIR__),
                [
                    'filter' => (new PathMatcher())
                        ->only(self::ONLY_CONTROLLERS)
                    ,
                    'recursive' => true,
                ]
            )
        );

        foreach ($io->progressIterate($files) as $file) {
            $className = $this->getClassName($file);

            if (!class_exists($className)) {
                continue;
            }

            $this->processClass(new ReflectionClass($className));
        }

        if ($this->errors) {
            $io->error($this->errors);
            return Command::FAILURE;
        }

        $this->manager->assign(RbamRole::admin->getItemName(), $userId);

        $io->success($this->translator->translate('rbac.success'));
        $io->info(explode("\n", $this->translator->translate('rbac.next')));
        return Command::SUCCESS;
    }

    private function getClassName(string $file): string
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

        return $namespace . '\\' . $class;
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

        foreach ($reflectionClass->getAttributes(RoleAttribute::class) as $attribute) {
            $attribute = $attribute->newInstance();

            $name = $attribute->getName();

            try {
                $this->manager->addRole(new Role($name));
            } catch (ItemAlreadyExistsException) {
                // Don't do anything
            }

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

            if ($parent = $attribute->getParent()) {
                try {
                    $this->manager->addRole(new Role($parent));
                } catch (ItemAlreadyExistsException) {
                    // Don't do anything
                }

                if ($this->manager->canAddChild($parent, $name)) {
                    $this->manager->addChild($parent, $name);
                }
            }

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
        } catch (ItemAlreadyExistsException) {
            // Don't do anything
        }

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

        if ($parent = $attribute->getParent()) {
            try {
                $this->manager->addRole(new Role($parent));
            } catch (ItemAlreadyExistsException) {
                // Don't do anything
            }

            if ($this->manager->canAddChild($parent, $name)) {
                $this->manager->addChild($parent, $name);
            }
        }

        return $name;
    }
}