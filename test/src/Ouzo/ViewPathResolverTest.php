<?php

namespace Ouzo;

class ViewPathResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnHtmlPathForTextContentType()
    {
        //given
        $type = 'text/html';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.phtml', $path);
    }

    /**
     * @test
     */
    public function shouldReturnXmlPathForXmlContentType()
    {
        //given
        $type = 'text/xml';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.xml.phtml', $path);
    }

    /**
     * @test
     */
    public function shouldReturnAjaxPathForAjax()
    {
        //given
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', 'text/xml');

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.ajax.phtml', $path);
    }

    /**
     * @test
     */
    public function shouldReturnJsonPath()
    {
        //given
        $type = 'application/json';

        //when
        $path = ViewPathResolver::resolveViewPath('exception', $type);

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.json.phtml', $path);
    }
}
