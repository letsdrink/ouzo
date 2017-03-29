<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Config\ConfigRepository;
use Ouzo\ExceptionHandling\DebugErrorHandler;
use Ouzo\ExceptionHandling\ErrorHandler;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class Bootstrap
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;
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

    /**
     * @param $config
     * @return $this
     */
    public function addConfig($config)
    {
        $this->configRepository = Config::registerConfig($config);
        return $this;
    }

    /**
     * @param Injector $injector
     * @return $this
     */
    public function withInjector(Injector $injector)
    {
        $this->injector = $injector;
        return $this;
    }

    /**
     * @param InjectorConfig $config
     * @return $this
     */
    public function withInjectorConfig(InjectorConfig $config)
    {
        $this->injectorConfig = $config;
        return $this;
    }

    /**
     * @return void
     */
    public function runApplication()
    {
        if ($this->configRepository) {
            $this->configRepository->reload();
        }

        $this->registerErrorHandlers();

        $this->includeRoutes();
        $this->createFrontController()->init();
    }

    /**
     * @return void
     */
    private function registerErrorHandlers()
    {
        if (Config::getValue('debug')) {
            $handler = new DebugErrorHandler();
        } else {
            $handler = new ErrorHandler();
        }
        $handler->register();
    }

    /**
     * @return void
     */
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
        return $injector->getInstance(FrontController::class);
    }
}
