<?php
namespace Thulium;

class Bootstrap
{
    public static function runApplication()
    {
        error_reporting(E_ALL);

        putenv('environment=prod');

        define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

        set_exception_handler('\Thulium\Error::exceptionHandler');
        set_error_handler('\Thulium\Error::errorHandler');
        register_shutdown_function('\Thulium\Error::shutdownHandler');

        $loader = new \Thulium\Loader();
        $loader
            ->setIncludePath('application/')
            ->setIncludePath('vendor/thulium/framework/lib/')
            ->setIncludePath('locales/')
            ->register();

        $controller = new FrontController();
        $controller->init();
    }
}