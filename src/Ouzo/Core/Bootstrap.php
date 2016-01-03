<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class Bootstrap
{
    /**
     * @var Injector
     */
    private $injector;
    /**
     * @var InjectorConfig
     */
    private $injectorConfig;

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

    public function withInjector(Injector $injector)
    {
        $this->injector = $injector;
        return $this;
    }

    public function withInjectorConfig(InjectorConfig $config)
    {
        $this->injectorConfig = $config;
        return $this;
    }

    public function runApplication()
    {
        set_exception_handler('\Ouzo\ExceptionHandling\ErrorHandler::exceptionHandler');
        set_error_handler('\Ouzo\ExceptionHandling\ErrorHandler::errorHandler');
        register_shutdown_function('\Ouzo\ExceptionHandling\ErrorHandler::shutdownHandler');

        $this->includeRoutes();
        $this->createFrontController()->init();
    }

    private function includeRoutes()
    {
        $routesPath = Path::join(ROOT_PATH, 'config', 'routes.php');
        Files::loadIfExists($routesPath);
    }

    /**
     * @return FrontController
     */
    private function createFrontController()
    {
        $injector = $this->injector ?: new Injector($this->injectorConfig);
        return $injector->getInstance('\Ouzo\FrontController');
    }
}
