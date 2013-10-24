<?php
error_reporting(E_ALL);

putenv('environment=test');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

require_once ROOT_PATH . 'src/Ouzo/Loader.php';
require_once ROOT_PATH . 'src/Ouzo/FrontController.php';
require_once ROOT_PATH . 'src/Ouzo/Error.php';

$loader = new \Ouzo\Loader();
$loader
    ->setIncludePath('test/')
    ->setIncludePath('src/')
    ->register();