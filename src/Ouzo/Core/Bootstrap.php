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
use Ouzo\Middleware\Interceptor\DefaultRequestId;
use Ouzo\Middleware\Interceptor\LogRequest;
use Ouzo\Middleware\MiddlewareRepository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Interceptor;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Path;

class Bootstrap
{
    /** @var ConfigRepository */
    private $configRepository;
    /** @var Injector */
    private $injector;
    /** @var InjectorConfig */
    private $injectorConfig;
    /** @var Interceptor[] */
    private $interceptors = [];
    /** @var bool */
    private $overrideMiddleware = false;

    public function __construct(EnvironmentSetter $environmentSetter = null)
    {
        error_reporting(E_ALL);
        $environmentSetter = $environmentSetter ?: new EnvironmentSetter('prod');
        $environmentSetter->set();
    }

    /**
     * @param object $config
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
     * @param Interceptor $interceptors
     * @return $this
     */
    public function withMiddleware(...$interceptors)
    {
        $this->interceptors = Arrays::filter($interceptors, Functions::isInstanceOf(Interceptor::class));

        return $this;
    }

    /**
     * @param Interceptor $interceptors
     * @return $this
     */
    public function overrideMiddleware(...$interceptors)
    {
        $this->interceptors = Arrays::filter($interceptors, Functions::isInstanceOf(Interceptor::class));
        $this->overrideMiddleware = true;

        return $this;
    }

    /** @return FrontController */
    public function runApplication()
    {
        if ($this->configRepository) {
            $this->configRepository->reload();
        }

        $this->registerErrorHandlers();
        $this->includeRoutes();

        $frontController = $this->createFrontController();
        $frontController->init();

        return $frontController;
    }

    /** @return void */
    private function registerErrorHandlers()
    {
        if (Config::getValue('debug')) {
            $handler = new DebugErrorHandler();
        } else {
            $handler = new ErrorHandler();
        }
        $handler->register();
    }

    /** @return void */
    private function includeRoutes()
    {
        $routesPath = Path::join(ROOT_PATH, 'config', 'routes.php');
        Files::loadIfExists($routesPath);
    }

    /** @return FrontController */
    private function createFrontController()
    {
        $middlewareRepository = $this->createMiddlewareRepository();

        $injector = $this->createInjector();
        $injector->getInjectorConfig()
            ->bind(MiddlewareRepository::class)->toInstance($middlewareRepository);

        return $injector->getInstance(FrontController::class);
    }

    /** @return Injector */
    private function createInjector()
    {
        $injectorConfig = $this->injectorConfig ?: new InjectorConfig();

        return $this->injector ?: new Injector($injectorConfig);
    }

    /** @return MiddlewareRepository */
    private function createMiddlewareRepository()
    {
        $middlewareRepository = new MiddlewareRepository();

        if (!$this->overrideMiddleware) {
            $middlewareRepository
                ->add(new DefaultRequestId())
                ->add(new LogRequest());
        }
        $middlewareRepository->addAll($this->interceptors);

        return $middlewareRepository;
    }
}
