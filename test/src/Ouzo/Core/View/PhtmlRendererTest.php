<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\View\PhtmlRenderer;

class PhtmlRendererTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Config::overrideProperty('path', 'view')->with('test\src\Ouzo\Core\View');
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::revertProperty('path', 'view');
    }

    /**
     * @test
     */
    public function shouldRenderView()
    {
        //given
        $renderer = new PhtmlRenderer('hello_world', []);

        //when
        $result = $renderer->render();

        //then
        $this->assertEquals('Hello World!', $result);
    }

    /**
     * @test
     */
    public function shouldRenderViewWithAttributes()
    {
        //given
        $renderer = new PhtmlRenderer('hello', ['name' => 'Jack']);

        //when
        $result = $renderer->render();

        //then
        $this->assertEquals('Hello Jack!', $result);
    }
}
