<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command;

use BeastBytes\Yii\Rbam\Command\Attribute\Permission as PermissionAttribute;
use BeastBytes\Yii\Rbam\Controller\RbamController;
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
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;

#[AsCommand(name: 'rbam:initialise', description: 'Initialise RBAM')]
class InitialiseCommand extends Command
{
    public function __construct(
        private readonly ItemsStorageInterface $itemsStorage,
        private readonly ManagerInterface $manager,
        private readonly TranslatorInterface $translator,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'uid',
                InputArgument::REQUIRED,
                'User ID to be assigned Rbam Role.',
            )
            ->setHelp('Initialises RBAM')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (count($this->itemsStorage->getRoles()) === 0) {
            $now = time();
            $this
                ->manager
                ->addRole((new Role(RbamController::RBAM_ROLE))
                    ->withDescription($this->translator->translate('title.rbam'))
                    ->withCreatedAt($now)
                    ->withUpdatedAt($now)
                )
            ;
            $this
                ->manager
                ->assign(RbamController::RBAM_ROLE, $input->getArgument('uid'));

            foreach (RbamPermission::cases() as $permission) {
                $this
                    ->manager
                    ->addPermission((new Permission($permission->name))
                        ->withDescription($this->translator->translate('permission.' . $permission->value))
                        ->withCreatedAt($now)
                        ->withUpdatedAt($now)
                    )
                ;

                $this
                    ->manager
                    ->addChild(RbamController::RBAM_ROLE, $permission->name);
            }

            $io->success(
                $this
                    ->translator
                    ->translate('flash.rbam-initialised')
            );
        } else {
            $io->info(
                $this
                    ->translator
                    ->translate('flash.rbam-already-initialised')
            );
        }

        return Command::SUCCESS;
    }
}