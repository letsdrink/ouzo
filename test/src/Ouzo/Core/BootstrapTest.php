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
use Ouzo\EnvironmentSetter;
use Ouzo\HeaderSender;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Middleware\Interceptor\SessionStarter;
use Ouzo\OutputRenderer;
use Ouzo\RedirectHandler;
use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\MockCookiesSetter;
use Ouzo\Tests\MockDownloadHandler;
use Ouzo\Tests\MockHeaderSender;
use Ouzo\Tests\MockOutputRenderer;
use Ouzo\Tests\MockRedirectHandler;
use Ouzo\Tests\MockSessionStarterInterceptor;
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
    /** @var InjectorConfig */
    private $config;
    /** @var Bootstrap */
    private $bootstrap;

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
        $this->bootstrap = new Bootstrap(new EnvironmentSetter('test'));
        $this->bootstrap->withInjectorConfig($this->config);

        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['REDIRECT_QUERY_STRING']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Config::clearProperty('debug');
    }

    /**
     * @test
     */
    public function shouldBindMiddlewareWithInterceptors()
    {
        //when
        $frontController = $this->bootstrap
            ->withMiddleware(SampleMiddleware::class)
            ->runApplication();

        //then
        $interceptors = $frontController->getMiddlewareRepository()->getInterceptors();
        Assert::thatArray($interceptors)->isNotEmpty();
    }

    /**
     * @test
     */
    public function shouldOverrideMiddleware()
    {
        //when
        $frontController = $this->bootstrap
            ->overrideMiddleware(SampleMiddleware::class, MockSessionStarterInterceptor::class)
            ->runApplication();

        //then
        $interceptors = $frontController->getMiddlewareRepository()->getInterceptors();
        Assert::thatArray($interceptors)->hasSize(2);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenMiddlewareClassNotImplementingInterceptorInterface()
    {
        //when
        CatchException::when($this->bootstrap->withMiddleware(stdClass::class))->runApplication();

        //then
        CatchException::assertThat()->hasMessage('stdClass class is not implementing Interceptor interface');
    }
}
