<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Command;

use BeastBytes\Yii\Rbam\Permission as RbamPermission;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;
use Yiisoft\Translator\TranslatorInterface;

#[AsCommand(name: 'rbam:initialise', description: 'Initialise RBAM')]
final class InitialiseCommand extends Command
{
    private array $roles = [
        'RbamItemsManager' => [
            'children' => [
                'RbamIndex',
                'RbacItemCreate',
                'RbacItemRemove',
                'RbacItemUpdate',
                'RbacItemView',
            ],
            'description' => 'role.rbam-items-manager',
        ],
        'RbamRulesManager' => [
            'children' => [
                'RbamIndex',
                'RbacRuleCreate',
                'RbacRuleDelete',
                'RbacRuleUpdate',
                'RbacRuleView',
            ],
            'description' => 'role.rbam-rules-manager',
        ],
        'RbamUsersManager' => [
            'children' => [
                'RbamIndex',
                'RbacUserUpdate',
                'RbacUserView',
            ],
            'description' => 'role.rbam-users-manager',
        ],
        'Rbam' => [
            'children' => [
                'RbamItemsManager',
                'RbamRulesManager',
                'RbamUsersManager',
            ],
            'description' => 'role.rbam',
        ],
    ];

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

            foreach (RbamPermission::cases() as $permission) {
                $this
                    ->manager
                    ->addPermission((new Permission($permission->name))
                        ->withDescription($this->translator->translate('permission.' . $permission->value))
                        ->withCreatedAt($now)
                        ->withUpdatedAt($now)
                    )
                ;
            }

            foreach ($this->roles as $name => $config) {
                $this
                    ->manager
                    ->addRole((new Role($name))
                        ->withDescription($this->translator->translate($config['description']))
                        ->withCreatedAt($now)
                        ->withUpdatedAt($now)
                    )
                ;

                foreach ($config['children'] as $child) {
                    $this
                        ->manager
                        ->addChild($name, $child)
                    ;
                }
            }

            $this
                ->manager
                ->assign('Rbam', $input->getArgument('uid'));

            $io->success(
                $this
                    ->translator
                    ->translate('message.rbam-initialised')
            );
        } else {
            $io->info(
                $this
                    ->translator
                    ->translate('message.rbam-already-initialised')
            );
        }

        return Command::SUCCESS;
    }
}