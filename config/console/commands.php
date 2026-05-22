<?php

declare(strict_types=1);

use BeastBytes\Yii\Rbam\Command\InitialiseCommand;
use BeastBytes\Yii\Rbam\Command\RbacCommand;

return [
    'rbac:initialise' => RbacCommand::class,
    /*
    'serve' => Serve::class,
    'user:create' => App\User\Console\CreateCommand::class,
    'user:assignRole' => App\User\Console\AssignRoleCommand::class,
    'fixture:add' => App\Command\Fixture\AddCommand::class,
    'fixture:schema:clear' => App\Command\Fixture\SchemaClearCommand::class,
    */
];