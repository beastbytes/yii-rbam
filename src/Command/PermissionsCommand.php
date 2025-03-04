<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command;

use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Files\FileHelper;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;

#[AsCommand(name: 'rbac:addPermissions', description: 'Create RBAC permissions')]
class PermissionsCommand extends Command
{
    private ?string $errorMessage = null;
    /** @var list<string> */
    private array $except = ['./config/**', './resources/**', './tests/**', './vendor/**'];
    /** @var list<string> */
    private array $only = ['**Controller.php'];
    private SymfonyStyle $io;

    public function __construct(private readonly ManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'except',
                'E',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Exclude path from source files.',
                [],
            )
            ->addOption(
                'only',
                'O',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Use the Only specified pattern for matching source files.',
                [],
            )
            ->addArgument(
                'src',
                InputArgument::OPTIONAL,
                'Path for source files.',
            )
            ->setHelp('Scans the project files and adds RBAC permissions based on the Permission attributes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        /** @var string $src */
        $src = $input->getArgument('src') ?? getcwd();
        /** @var string[] $except */
        $except = $input->getOption('except');
        /** @var string[] $only */
        $only = $input->getOption('only');

        if (!empty($except)) {
            $this->except = $except;
        }

        if (!empty($only)) {
            $this->only = $only;
        }

        $files = FileHelper::findFiles($src, [
            'filter' => (new PathMatcher())
                ->only(...$this->only)
                ->except(...$this->except),
            'recursive' => true,
        ]);

        foreach ($files as $file) {
            $this->processFile($file);

            if ($this->errorMessage !== null) {
                break;
            }
        }

        if ($this->errorMessage === null) {
            $this->io->success('Permissions successfully created');
            return Command::SUCCESS;
        } else {
            $this->io->error($this->errorMessage);
            return Command::FAILURE;
        }
    }

    private function processFile(string $file): void
    {
        $className = $this->getClassName($file);

        if (!class_exists($className)) {
            return;
        }

        $reflectionClass = new ReflectionClass($className);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $attributes = $reflectionMethod->getAttributes(PermissionAttribute::class);

            foreach ($attributes as $attribute) {
                $now = time();
                $arguments = $attribute->getArguments();

                if (!key_exists('name', $arguments)) {
                    $this->errorMessage = (sprintf(
                        'Name not set for Permission attribute in %s::%s',
                        $reflectionClass->getName(),
                        $reflectionMethod->getName()
                    ));

                    return;
                }

                $name = is_string($arguments['name']) ? $arguments['name'] : $arguments['name']->value;

                if (
                    $this->manager->getRole($name) !== null
                    || $this->manager->getPermission($name) !== null
                ) {
                    $this->io->write(sprintf('RBAC Item with name "%s" exists', $name), true);
                } else {
                    $permission = (new Permission($name))
                        ->withCreatedAt($now)
                        ->withUpdatedAt($now)
                    ;

                    foreach (['description', 'ruleName'] as $argument) {
                        if (key_exists($argument, $arguments)) {
                            $with = 'with' . ucfirst($argument);
                            $permission = $permission->$with($arguments[$argument]);
                        }
                    }

                    $this
                        ->manager
                        ->addPermission($permission)
                    ;

                    $this->io->write(sprintf('Added %s Permission', $name), true);
                }

                if (key_exists('parent', $arguments)) {
                    $parent = is_string($arguments['parent']) ? $arguments['parent'] : $arguments['parent']->value;

                    if ($this
                            ->manager
                            ->canAddChild($parent, $name)
                    ) {
                        $this
                            ->manager
                            ->addChild($parent, $name)
                        ;
                        $this->io->write(
                            sprintf('Added "%s" as child of "%s"', $name, $parent),
                            true
                        );
                    } else {
                        $this->io->write(
                            sprintf('Unable to add "%s" as child of "%s"', $name, $parent),
                            true
                        );
                    }
                }
            }
        }
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
}