<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command;

use BeastBytes\Yii\Rbam\InitialisationService;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Item as ItemAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Rbac\Attribute\Role as RoleAttribute;
use BeastBytes\Yii\Rbam\Rbac\Role as RbamRole;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Safe\Exceptions\DirException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Files\FileHelper;
use Yiisoft\Files\PathMatcher\PathMatcher;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\Exception\ItemAlreadyExistsException;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;

#[AsCommand(
    name: 'rbac:initialise',
    description: 'Initialise RBAC Permissions and Roles',
    help: 'Scans the project files and creates RBAC items and hierarchy from attributes'
)]
final class RbacInitialiseCommand extends Command
{
    private const array EXCEPT = ['./config/**', './resources/**', './tests/**', './vendor/**'];
    private const array ONLY_CONTROLLERS = ['**Controller.php'];

    /** @var list<string> $errors */
    private array $errors = [];
    private SymfonyStyle $io;

    public function __construct(
        private readonly InitialisationService $initialisationService,
        private readonly ManagerInterface $manager,
        private readonly TranslatorInterface $translator,
    )
    {
        parent::__construct();
    }

    /**
     * @throws DirException
     * @throws ReflectionException
     */
    public function __invoke(
        SymfonyStyle $io,

        #[Argument(description: 'Path for source files (default is current working directory)')]
        string $src = null,

        #[Option(description: 'Exclude path from source files', name: 'except', shortcut: 'E')]
        array $except = self::EXCEPT,

        #[Option(description: 'Use Only the specified pattern for matching source files', name: 'only', shortcut: 'O')]
        array $only = self::ONLY_CONTROLLERS,
    ): int
    {
        $io->title($this->translator->translate('rbac.title'));

        $files = FileHelper::findFiles(
            $src ?? \Safe\getcwd(),
            [
                'filter' => (new PathMatcher())
                    ->only(...$only)
                    ->except(...$except),
                'recursive' => true,
            ]
        );

        foreach ($io->progressIterate($files) as $file) {
            $this->initialisationService->processFile($file);
        }

        if ($this->initialisationService->hasErrors()) {
            $io->error($this->initialisationService->getErrors());
            return Command::FAILURE;
        }

        $io->success($this->translator->translate('rbac.success'));
        $io->info(explode("\n", $this->translator->translate('rbac.next')));
        return Command::SUCCESS;
    }
}