<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Closure;
use InvalidArgumentException;
use Ouzo\Config\ConfigRepository;
use Ouzo\ExceptionHandling\DebugErrorHandler;
use Ouzo\ExceptionHandling\DebugExceptionHandler;
use Ouzo\ExceptionHandling\ErrorHandler;
use Ouzo\ExceptionHandling\ExceptionHandler;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\Scope;
use Ouzo\Middleware\Interceptor\DefaultRequestId;
use Ouzo\Middleware\Interceptor\LogRequest;
use Ouzo\Middleware\Interceptor\SessionStarter;
use Ouzo\Middleware\MiddlewareRepository;
use Ouzo\Request\RoutingService;
use Ouzo\Uri\PathProvider;
use Ouzo\Uri\PathProviderInterface;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Chain\Interceptor;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class Bootstrap
{
    private ?ConfigRepository $configRepository = null;
    private ?Injector $injector = null;
    private ?InjectorConfig $injectorConfig = null;
    private ?Closure $injectorCallback = null;
    /** @var string[] */
    private array $interceptors = [];
    private bool $overrideMiddleware = false;
    private ?ErrorHandler $errorHandler = null;

    public function __construct(Environment $environment)
    {
        error_reporting(E_ALL);
        $environment->init();
    }

    public function addConfig(object $config): static
    {
        $this->configRepository = Config::registerConfig($config);
        return $this;
    }

    public function withInjector(Injector $injector): static
    {
        $this->injector = $injector;
        return $this;
    }

    public function withInjectorConfig(InjectorConfig $config): static
    {
        $this->injectorConfig = $config;
        return $this;
    }

    public function configureInjector(Closure $callback): static
    {
        $this->injectorCallback = $callback;
        return $this;
    }

    /** @param string[] $interceptors */
    public function withMiddleware(...$interceptors): static
    {
        $this->interceptors = $interceptors;
        return $this;
    }

    /** @param string[] $interceptors */
    public function overrideMiddleware(...$interceptors): static
    {
        $this->interceptors = $interceptors;
        $this->overrideMiddleware = true;
        return $this;
    }

    public function withErrorHandler(ErrorHandler $errorHandler): static
    {
        $this->errorHandler = $errorHandler;
        return $this;
    }

    public function runApplication(): FrontController
    {
        if ($this->configRepository) {
            $this->configRepository->reload();
        }

        $this->registerErrorHandlers();
        $this->includeRoutes();

        $injector = $this->setupInjector();

        /** @var FrontController $frontController */
        $frontController = $injector->getInstance(FrontController::class);
        $frontController->init();

        return $frontController;
    }

    private function registerErrorHandlers(): void
    {
        if (Config::getValue('debug')) {
            (new DebugErrorHandler(new DebugExceptionHandler()))->register();
            return;
        }

        if (!is_null($this->errorHandler)) {
            $this->errorHandler->register();
            return;
        }
        (new ErrorHandler(new ExceptionHandler()))->register();
    }

    private function includeRoutes(): void
    {
        $routesPath = Path::join(ROOT_PATH, 'config', 'routes.php');
        Files::loadIfExists($routesPath);
    }

    public function setupInjector(): Injector
    {
        $injector = $this->createInjector();

        $config = $injector->getInjectorConfig();
        $config->bind(RoutingService::class)->in(Scope::SINGLETON);
        $config->bind(PathProviderInterface::class)->to(PathProvider::class)->in(Scope::SINGLETON);

        $middlewareRepository = $this->createMiddlewareRepository($injector);
        $config->bind(MiddlewareRepository::class)->toInstance($middlewareRepository);

        if ($this->injectorCallback !== null) {
            call_user_func($this->injectorCallback, $injector);
        }

        return $injector;
    }

    private function createInjector(): Injector
    {
        $injectorConfig = $this->injectorConfig ?: new InjectorConfig();
        return $this->injector ?: new Injector($injectorConfig);
    }

    private function createMiddlewareRepository(Injector $injector): MiddlewareRepository
    {
        $middlewareRepository = new MiddlewareRepository();

        if (!$this->overrideMiddleware) {
            /** @var SessionStarter $sessionStarter */
            $sessionStarter = $injector->getInstance(SessionStarter::class);
            /** @var DefaultRequestId $defaultRequestId */
            $defaultRequestId = $injector->getInstance(DefaultRequestId::class);
            /** @var LogRequest $logRequest */
            $logRequest = $injector->getInstance(LogRequest::class);
            $middlewareRepository
                ->add($sessionStarter)
                ->add($defaultRequestId)
                ->add($logRequest);
        }

        $interceptors = Arrays::map($this->interceptors, $this->createInterceptor($injector));
        $middlewareRepository->addAll($interceptors);

        return $middlewareRepository;
    }

    private function createInterceptor(Injector $injector): Closure
    {
        return function ($interceptor) use ($injector) {
            $instance = $injector->getInstance($interceptor);
            if (!$instance instanceof Interceptor) {
                throw new InvalidArgumentException("{$interceptor} class is not implementing Interceptor interface");
            }
            return $instance;
        };
    }
}
