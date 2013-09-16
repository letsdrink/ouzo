<?php

use Ouzo\Utilities\Clock;

error_reporting(E_ALL);

putenv('environment=test');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

require_once ROOT_PATH . 'vendor/letsdrink/ouzo/lib/Ouzo/Loader.php';
require_once ROOT_PATH . 'vendor/letsdrink/ouzo/lib/Ouzo/FrontController.php';
require_once ROOT_PATH . 'vendor/letsdrink/ouzo/lib/Ouzo/Error.php';

$loader = new \Ouzo\Loader();
$loader
    ->setIncludePath('custom/')
    ->setIncludePath('application/')
    ->setIncludePath('vendor/thulium/framework/lib/')
    ->setIncludePath('locales/')
    ->setIncludePath('test/application/')
    ->setIncludePath('test/seed/')
    ->setIncludePath('seed/')
    ->register();

Clock::freeze();
