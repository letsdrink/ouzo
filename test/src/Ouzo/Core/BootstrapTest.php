<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Bootstrap;
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\CookiesSetter;
use Ouzo\DownloadHandler;
use Ouzo\Environment;
use Ouzo\ExceptionHandling\ErrorHandler;
use Ouzo\FrontController;
use Ouzo\HeaderSender;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Middleware\Interceptor\SessionStarter;
use Ouzo\OutputRenderer;
use Ouzo\RedirectHandler;
use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Tests\MockCookiesSetter;
use Ouzo\Tests\MockDownloadHandler;
use Ouzo\Tests\MockHeaderSender;
use Ouzo\Tests\MockOutputRenderer;
use Ouzo\Tests\MockRedirectHandler;
use Ouzo\Tests\MockSessionStarterInterceptor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BootstrapSampleController extends Controller
{
    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }
}

class BootstrapTest extends TestCase
{
    private InjectorConfig $config;
    private Bootstrap $bootstrap;
    private bool $handlersRegistered = false;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->config = new InjectorConfig();
        $this->config->bind(OutputRenderer::class)->toInstance(new MockOutputRenderer());
        $this->config->bind(HeaderSender::class)->toInstance(new MockHeaderSender());
        $this->config->bind(CookiesSetter::class)->toInstance(new MockCookiesSetter());
        $this->config->bind(RedirectHandler::class)->toInstance(new MockRedirectHandler());
        $this->config->bind(SessionStarter::class)->toInstance(new MockSessionStarterInterceptor());
        $this->config->bind(DownloadHandler::class)->toInstance(new MockDownloadHandler());
    }

    public function setUp(): void
    {
        parent::setUp();
        Route::clear();

        Route::get('/', BootstrapSampleController::class, 'index');

        /** @var Environment|MockInterface $environment */
        $environment = Mock::create(Environment::class);

        $this->bootstrap = new Bootstrap($environment);
        $this->bootstrap->withInjectorConfig($this->config);
        $this->handlersRegistered = false;

        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['REDIRECT_QUERY_STRING']);
    }

    public function tearDown(): void
    {
        if ($this->handlersRegistered) {
            restore_error_handler();
            restore_exception_handler();
        }
        parent::tearDown();
        Config::clearProperty('debug');
    }

    private function runBootstrapApplication(): FrontController
    {
        $result = $this->bootstrap->runApplication();
        $this->handlersRegistered = true;
        return $result;
    }

    #[Test]
    public function shouldBindMiddlewareWithInterceptors()
    {
        //when
        $this->bootstrap->withMiddleware(SampleMiddleware::class);
        $frontController = $this->runBootstrapApplication();

        //then
        $interceptors = $frontController->getMiddlewareRepository()->getInterceptors();
        Assert::thatArray($interceptors)->isNotEmpty();
    }

    #[Test]
    public function shouldOverrideMiddleware()
    {
        //when
        $this->bootstrap->overrideMiddleware(SampleMiddleware::class, MockSessionStarterInterceptor::class);
        $frontController = $this->runBootstrapApplication();

        //then
        $interceptors = $frontController->getMiddlewareRepository()->getInterceptors();
        Assert::thatArray($interceptors)->hasSize(2);
    }

    #[Test]
    public function shouldThrowExceptionWhenMiddlewareClassNotImplementingInterceptorInterface()
    {
        //when
        CatchException::when($this->bootstrap->withMiddleware(stdClass::class))->runApplication();
        $this->handlersRegistered = true;

        //then
        CatchException::assertThat()->hasMessage('stdClass class is not implementing Interceptor interface');
    }

    #[Test]
    public function shouldRegisterCustomErrorHandler()
    {
        //given
        Config::overrideProperty('debug')->with(false);
        /** @var ErrorHandler|MockInterface $errorHandler */
        $errorHandler = Mock::create(ErrorHandler::class);

        //when
        $this->bootstrap
            ->withErrorHandler($errorHandler)
            ->runApplication();

        //then
        Mock::verify($errorHandler)->register();
    }
}
