<?php
error_reporting(E_ALL);

putenv('environment=test');

define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

mb_internal_encoding("UTF-8");

require 'vendor/autoload.php';
