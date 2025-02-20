<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    'COOKIE_SECRET',
    'YII_DEBUG',
    'YII_ENV'
])
    ->notEmpty()
;

$_ENV['YII_ENV'] = empty($_ENV['YII_ENV']) ? null : (string)$_ENV['YII_ENV'];
$_SERVER['YII_ENV'] = $_ENV['YII_ENV'];

$_ENV['YII_DEBUG'] = filter_var(
    !empty($_ENV['YII_DEBUG']) ? $_ENV['YII_DEBUG'] : true,
    FILTER_VALIDATE_BOOLEAN,
    FILTER_NULL_ON_FAILURE
) ?? true;
$_SERVER['YII_DEBUG'] = $_ENV['YII_DEBUG'];