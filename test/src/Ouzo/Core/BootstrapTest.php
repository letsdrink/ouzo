<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Bootstrap;
use Ouzo\Config;
use Ouzo\CookiesSetter;
use Ouzo\DownloadHandler;
use Ouzo\EnvironmentSetter;
use Ouzo\HeaderSender;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Middleware\Interceptor\SessionStarter;
use Ouzo\OutputDisplayer;
use Ouzo\RedirectHandler;
use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Tests\MockCookiesSetter;
use Ouzo\Tests\MockDownloadHandler;
use Ouzo\Tests\MockHeaderSender;
use Ouzo\Tests\MockOutputDisplayer;
use Ouzo\Tests\MockRedirectHandler;
use Ouzo\Tests\MockSessionStarterInterceptor;
use PHPUnit\Framework\TestCase;

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
        $this->config->bind(OutputDisplayer::class)->toInstance(new MockOutputDisplayer());
        $this->config->bind(HeaderSender::class)->toInstance(new MockHeaderSender());
        $this->config->bind(CookiesSetter::class)->toInstance(new MockCookiesSetter());
        $this->config->bind(RedirectHandler::class)->toInstance(new MockRedirectHandler());
        $this->config->bind(SessionStarter::class)->toInstance(new MockSessionStarterInterceptor());
        $this->config->bind(DownloadHandler::class)->toInstance(new MockDownloadHandler());
    }

    public function setUp()
    {
        parent::setUp();
        Config::overrideProperty('namespace', 'controller')->with('\\Ouzo\\');
        Route::clear();

        Route::get('/', 'sample#save');
        $this->bootstrap = new Bootstrap(new EnvironmentSetter('test'));
        $this->bootstrap->withInjectorConfig($this->config);
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
        Config::clearProperty('debug');
    }

    /**
     * @test
     */
    public function shouldBindMiddlewareWithInterceptors()
    {
        //when
        $frontController = $this->bootstrap
            ->withMiddleware(new SampleMiddleware())
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
            ->overrideMiddleware(new SampleMiddleware(),new MockSessionStarterInterceptor())
            ->runApplication();

        //then
        $interceptors = $frontController->getMiddlewareRepository()->getInterceptors();
        Assert::thatArray($interceptors)->hasSize(2);
    }
}
