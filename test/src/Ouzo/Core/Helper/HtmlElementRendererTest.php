<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Helper\HtmlElementRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlElementRendererTest extends TestCase
{
    #[Test]
    public function shouldRenderAnchorElement()
    {
        //given
        $renderer = HtmlElementRenderer::anchor()
            ->setText("some link")
            ->setAttributes(["data-a" => "'dta'\" txt", "id" => "element-id"])
            ->setAttribute("href", "https://link.to");

        //when
        $html = $renderer->render();

        //then
        $this->assertEquals('<a id="element-id" data-a="\'dta\'&quot; txt" href="https://link.to">some link</a>', $html);
    }

    #[Test]
    public function shouldRenderInputElementWithScript()
    {
        //given
        $renderer = HtmlElementRenderer::input("text")
            ->setAttribute("onclick", "window.location.href = 'https://link.to/?param=val'");

        //when
        $html = $renderer->render();

        //then
        $this->assertEquals('<input type="text" onclick="window.location.href = \'https://link.to/?param=val\'"/>', $html);
    }

    #[Test]
    public function shouldRenderConditionalFlag()
    {
        //given
        $renderer = HtmlElementRenderer::input("checkbox")
            ->setValue("1")
            ->setFlag("checked", true)
            ->setFlag("some_flag", false);

        //when
        $html = $renderer->render();

        //then
        $this->assertEquals('<input type="checkbox" value="1" checked/>', $html);
    }
}