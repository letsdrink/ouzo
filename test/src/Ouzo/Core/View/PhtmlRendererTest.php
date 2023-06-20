<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\View\DefaultViewPathProvider;
use Ouzo\View\PhtmlRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PhtmlRendererTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::overrideProperty('path', 'view')->with('test\src\Ouzo\Core\View');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Config::revertProperty('path', 'view');
    }

    #[Test]
    public function shouldRenderView()
    {
        //given
        $renderer = new PhtmlRenderer('hello_world', [], new DefaultViewPathProvider());

        //when
        $result = $renderer->render();

        //then
        $this->assertEquals('Hello World!', $result);
    }

    #[Test]
    public function shouldRenderViewWithAttributes()
    {
        //given
        $renderer = new PhtmlRenderer('hello', ['name' => 'Jack'], new DefaultViewPathProvider());

        //when
        $result = $renderer->render();

        //then
        $this->assertEquals('Hello Jack!', $result);
    }
}
