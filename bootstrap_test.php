<?php
error_reporting(E_ALL);

putenv('environment=test');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

require_once ROOT_PATH . 'lib/Thulium/Loader.php';
require_once ROOT_PATH . 'lib/Thulium/FrontController.php';
require_once ROOT_PATH . 'lib/Thulium/Error.php';

$loader = new \Thulium\Loader();
$loader
    ->setIncludePath('lib/')
    ->register();