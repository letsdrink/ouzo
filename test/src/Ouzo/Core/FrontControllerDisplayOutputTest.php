<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\HeaderSender;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Routing\Route;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;

class FrontControllerDisplayOutputTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Route::clear();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Config::clearProperty('debug');
    }

    protected function frontControllerBindings(InjectorConfig $config): void
    {
        parent::frontControllerBindings($config);
        $config->bind(HeaderSender::class)->toInstance(Mock::create(HeaderSender::class));
    }

    /**
     * @test
     */
    public function shouldNotDisplayOutputBeforeHeadersAreSent()
    {
        //given
        Route::allowAll('/sample', SampleController::class);

        $_SERVER['REQUEST_URI'] = '/sample/action';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->initFrontController();
        $obLevel = ob_get_level();

        //when
        Mock::when($this->frontController->getRequestExecutor()->getHeaderSender())->send(Mock::any())->thenAnswer(function () use ($obLevel) {
            //if there's a nested buffer, nothing was sent to output
            $this->assertTrue(ob_get_level() > $obLevel);
            $this->expectOutputString('OUTPUT');
        });

        //then
        $this->expectNotToPerformAssertions();
    }
}
