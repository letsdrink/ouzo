<?php

putenv('environment=prod');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

require '../vendor/autoload.php';

$loader = new \Ouzo\Loader();
$loader
    ->setIncludePath('./')
    ->register();

\Ouzo\Shell\Dispatcher::runScript($argv);
