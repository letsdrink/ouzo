<?php
namespace Thulium;

use Thulium\Config\CustomConfig;

class Bootstrap
{
    public function setConfig($config, $path = '')
    {
        Config::registerConfig(new CustomConfig($config, $path));
        return $this;
    }

    public function runApplication()
    {
        error_reporting(E_ALL);

        putenv('environment=prod');

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