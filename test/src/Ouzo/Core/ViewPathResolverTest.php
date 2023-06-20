<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ViewPathResolverTest extends TestCase
{
    #[Test]
    public function shouldReturnHtmlPathForTextContentType()
    {
        //given
        $type = 'text/html';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'exception.phtml', $path);
    }

    #[Test]
    public function shouldReturnXmlPathForXmlContentType()
    {
        //given
        $type = 'text/xml';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'exception.xml.phtml', $path);
    }

    #[Test]
    public function shouldReturnAjaxPathForAjax()
    {
        //given
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', '*/*');

        //then
        $this->assertEquals(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'exception.ajax.phtml', $path);
    }

    #[Test]
    public function shouldReturnJsonPath()
    {
        //given
        $type = 'application/json';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'exception.json.phtml', $path);
    }
}
