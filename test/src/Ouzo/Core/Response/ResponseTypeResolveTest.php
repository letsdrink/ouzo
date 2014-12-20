<?php
use Ouzo\ContentType;
use Ouzo\Response\ResponseTypeResolve;

class ResponseTypeResolveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnTypeFromAcceptHeader()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('application/json', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnBestMatchForAcceptHeader()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'text/*;q=0.7 , application/json;q=0.3';

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('text/html', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnContentTypeIfEmptyAccept()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = null;
        ContentType::set('application/json');

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('application/json', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnContentTypeIfWildcardInAccept()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = '*/*';
        ContentType::set('application/json');

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('application/json', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnHtmlIfNoAcceptAndNoContentType()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = null;
        ContentType::set(null);

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('text/html', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnRequestContentTypeForUnsupportedAccept()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'application/unsupported';
        ContentType::set('application/json');

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('application/json', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnHtmlForUnsupportedAcceptAndUnsupportedRequestContentType()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'application/unsupported';
        ContentType::set('application/unsupported');

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('text/html', $resolved);
    }

    /**
     * @test
     */
    public function shouldReturnHtmlForUnsupportedAcceptAndNoRequestContentType()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'application/unsupported';
        ContentType::set(null);

        //when
        $resolved = ResponseTypeResolve::resolve();

        //then
        $this->assertEquals('text/html', $resolved);
    }
}
