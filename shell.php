<?php
use Ouzo\Loader;
use Ouzo\Shell\Dispatcher;

putenv('environment=prod');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

if (file_exists(ROOT_PATH . 'test/application/config/routes.php')) {
    include_once ROOT_PATH . 'test/application/config/routes.php';
}

$loader = new Loader();
$loader
    ->setIncludePath('bin/')
    ->register();

Dispatcher::runScript($argv);