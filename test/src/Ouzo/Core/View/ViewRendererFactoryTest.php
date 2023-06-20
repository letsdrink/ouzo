<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\View\DefaultViewPathProvider;
use Ouzo\View\PhtmlRenderer;
use Ouzo\View\ViewRenderer;
use Ouzo\View\ViewRendererFactory;
use PHPUnit\Framework\TestCase;

class DummyRenderer implements ViewRenderer
{
    public function render(): string
    {
    }

    public function getViewPath(): string
    {
    }
}

class ViewRendererFactoryTest extends TestCase
{
    #[Test]
    public function shouldCreatePhtmlRendererWhenRendererWasNotConfigured()
    {
        //when
        $renderer = ViewRendererFactory::create('my_view', [], new DefaultViewPathProvider());

        //then
        $this->assertInstanceOf(PhtmlRenderer::class, $renderer);
    }

    #[Test]
    public function shouldCreateDefaultRendererAsSetInConfiguration()
    {
        //given
        Config::overrideProperty('renderer', 'default')->with('DummyRenderer');

        //when
        $renderer = ViewRendererFactory::create('my_view', [], new DefaultViewPathProvider());

        //then
        $this->assertInstanceOf('DummyRenderer', $renderer);
    }

    #[Test]
    public function shouldCreateRendererAsSetInConfigurationForParticularView()
    {
        //given
        Config::overrideProperty('renderer', 'my_view')->with('DummyRenderer');

        //when
        $renderer = ViewRendererFactory::create('my_view', [], new DefaultViewPathProvider());

        //then
        $this->assertInstanceOf('DummyRenderer', $renderer);
    }

    #[Test]
    public function shouldCreateRendererAsSetInConfigurationForParticularViewEvenThoughDefaultRendererIsSpecified()
    {
        //given
        Config::overrideProperty('renderer', 'default')->with('DefaultRenderer');
        Config::overrideProperty('renderer', 'my_view')->with('DummyRenderer');

        //when
        $renderer = ViewRendererFactory::create('my_view', [], new DefaultViewPathProvider());

        //then
        $this->assertInstanceOf('DummyRenderer', $renderer);
    }
}
