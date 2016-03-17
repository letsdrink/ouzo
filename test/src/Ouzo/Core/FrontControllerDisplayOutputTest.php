<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Routing\Route;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;

class FrontControllerDisplayOutputTest extends ControllerTestCase
{
    public function __construct()
    {
        Config::overrideProperty('namespace', 'controller')->with('\\Ouzo\\');
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        Route::clear();
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
        Config::clearProperty('debug');
        Config::clearProperty('callback', 'afterControllerInit');
    }

    protected function frontControllerBindings(InjectorConfig $config)
    {
        parent::frontControllerBindings($config);
        $config->bind('\Ouzo\HeaderSender')->toInstance(Mock::create());
    }

    /**
     * @test
     */
    public function shouldNotDisplayOutputBeforeHeadersAreSent()
    {
        //given
        $self = $this;

        $obLevel = ob_get_level();
        Mock::when($this->frontController->getHeaderSender())->send(Mock::any())->thenAnswer(function () use ($self, $obLevel) {
            //if there's a nested buffer, nothing was sent to output
            $self->assertTrue(ob_get_level() > $obLevel);
            $self->expectOutputString('OUTPUT');
        });

        Route::allowAll('/sample', 'sample');

        //when
        $this->get('/sample/action');

        //then no exceptions
    }
}
