<?php

namespace Thulium;

class ViewPathResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnHtmlPathForTextContentType()
    {
        //given
        $_SERVER["CONTENT_TYPE"] = 'text/html;encoding';

        //when
        $path = ViewPathResolver::resolveViewPath('exception');

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.phtml', $path);
    }

    /**
     * @test
     */
    public function shouldReturnXmlPathForXmlContentType()
    {
        //given
        $_SERVER["CONTENT_TYPE"] = 'text/xml;encoding';

        //when
        $path = ViewPathResolver::resolveViewPath('exception');

        //then
        $this->assertEquals(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.xml.phtml', $path);
    }
}
