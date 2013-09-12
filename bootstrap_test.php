<?php
error_reporting(E_ALL);

putenv('environment=test');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

require_once ROOT_PATH . 'lib/Ouzo/Loader.php';
require_once ROOT_PATH . 'lib/Ouzo/FrontController.php';
require_once ROOT_PATH . 'lib/Ouzo/Error.php';

$loader = new \Ouzo\Loader();
$loader
    ->setIncludePath('test/')
    ->setIncludePath('lib/')
    ->register();