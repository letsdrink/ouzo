<?php
namespace Thulium;

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
        set_exception_handler('\Thulium\Error::exceptionHandler');
        set_error_handler('\Thulium\Error::errorHandler');
        register_shutdown_function('\Thulium\Error::shutdownHandler');

        $loader = new \Thulium\Loader();
        $loader
            ->setIncludePath('application/')
            ->setIncludePath('vendor/letsdrink/ouzo/lib/')
            ->setIncludePath('locales/')
            ->register();

        $controller = new FrontController();
        $controller->init();
    }
}