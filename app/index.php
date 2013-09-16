<?php

use Ouzo\Bootstrap;

require 'vendor/autoload.php';

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

$bootstrap = new Bootstrap();
$bootstrap->runApplication();