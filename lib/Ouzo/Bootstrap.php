<?php
namespace Ouzo;

class Bootstrap
{
    public function __construct()
    {
        error_reporting(E_ALL);
        putenv('environment=prod');
    }

    public function addConfig($config)
    {
        Config::registerConfig($config);
        return $this;
    }

    public function runApplication()
    {
        set_exception_handler('\Ouzo\Error::exceptionHandler');
        set_error_handler('\Ouzo\Error::errorHandler');
        register_shutdown_function('\Ouzo\Error::shutdownHandler');

        $loader = new \Ouzo\Loader();
        $loader
            ->setIncludePath('application/')
            ->setIncludePath('vendor/letsdrink/ouzo/lib/')
            ->setIncludePath('locales/')
            ->register();

        $controller = new FrontController();
        $controller->init();
    }
}