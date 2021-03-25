<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\ContentType;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\SimpleMock;
use Ouzo\Tests\StreamStub;
use Ouzo\Uri;
use Ouzo\Uri\PathProvider;
use Ouzo\Utilities\JsonDecodeException;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    private Uri $uri;
    private PathProvider|SimpleMock $pathProviderMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->pathProviderMock = Mock::create(PathProvider::class);
        $this->uri = new Uri($this->pathProviderMock);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
    }

    /**
     * @test
     */
    public function shouldExtractController()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('User', $this->uri->getController());
        $this->assertEquals('user', $this->uri->getRawController());
    }

    /**
     * @test
     */
    public function shouldExtractAction()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('add', $this->uri->getAction());
    }

    /**
     * @test
     */
    public function shouldGetParamValueByName()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('john', $this->uri->getParam('name'));
        $this->assertEquals(5, $this->uri->getParam('id'));
    }

    /**
     * @test
     */
    public function shouldGetNullValueByNonExistingNameWhenAnyParamsPassed()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5');

        //then
        $this->assertNull($this->uri->getParam('surname'));
    }

    /**
     * @test
     */
    public function shouldGetNullValueByNonExistingNameWhenNoParamsPassed()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add');

        //then
        $this->assertNull($this->uri->getParam('surname'));
    }

    /**
     * @test
     */
    public function shouldHandleOddNumberOfParameters()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5/name');

        //when
        $param = $this->uri->getParam('name');

        //then
        $this->assertNull($param);
    }

    /**
     * @test
     */
    public function shouldSplitPathWithoutLimit()
    {
        //given
        $reflectionOfUri = $this->setPrivateMethodAccessible('parsePath');

        //when
        $paramsExpected = ['user', 'add', 'id', '5', 'name', 'john'];
        $callMethod = $reflectionOfUri->invoke(new Uri(new PathProvider()), '/user/add/id/5/name/john');

        //then
        $this->assertEquals($paramsExpected, $callMethod);
    }

    /**
     * @test
     */
    public function shouldSplitPathWithLimit()
    {
        //given
        $reflectionOfUri = $this->setPrivateMethodAccessible('parsePath');

        //when
        $paramsExpected = ['user', 'add', 'id/5/name/john'];
        $callMethod = $reflectionOfUri->invoke(new Uri(new PathProvider()), '/user/add/id/5/name/john', 3);

        //then
        $this->assertEquals($paramsExpected, $callMethod);
    }

    /**
     * @test
     */
    public function shouldGetAllParams()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5/name/john/surname/smith/');

        //when
        $params = $this->uri->getParams();
        $paramsExpected = ['id' => 5, 'name' => 'john', 'surname' => 'smith'];

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenAjaxRequest()
    {
        //given
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        //when
        $isAjax = Uri::isAjax();

        //then
        $this->assertTrue($isAjax);
    }

    /**
     * @test
     */
    public function shouldParseUrlWithParamsWhenGETDataAdded()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/5?param1=val1&param2=val2');

        //when
        $params = $this->uri->getParams();
        $paramsExpected = ['id' => 5, 'param1' => 'val1', 'param2' => 'val2'];

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldParseUrlWithoutParamsWhenGETDataAdded()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add?param1=val1&param2=val2&param3=t1%2Ct2%2Ct3');

        //when
        $params = $this->uri->getParams();
        $paramsExpected = ['param1' => 'val1', 'param2' => 'val2', 'param3' => 't1,t2,t3'];

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldParseUrlWhenSlashInGET()
    {
        //given
        $this->path(Config::getPrefixSystem() . '/user/add/id/4?param1=path/to/file&param2=val2');

        //when
        $params = $this->uri->getParams();
        $paramsExpected = ['id' => 4, 'param1' => 'path/to/file', 'param2' => 'val2'];

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldParsePutRequestInStream()
    {
        //given
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        StreamStub::register('put');
        StreamStub::$body = 'a=test2&t=test3';
        ContentType::set('application/x-www-form-urlencoded');

        //when
        $parameters = $this->getRequestParameters('put://input');

        //then
        ArrayAssert::that($parameters)
            ->hasSize(2)
            ->contains('test2')
            ->contains('test3');
        StreamStub::unregister();
    }

    public function shouldCorrectParseStream()
    {
        //given
        StreamStub::register('uri');
        StreamStub::$body = 'album%5Bdigital_photos%5D=false&name=john&phones%5B%5D=123&phones%5B%5D=456&phones%5B%5D=789&colors%5Bfloor%5D=red&colors%5Bdoors%5D=blue';

        //when
        $parameters = Uri::getRequestParameters('uri://input');

        //then
        ArrayAssert::that($parameters)->hasSize(4);
        StreamStub::unregister();
    }

    /**
     * @test
     */
    public function shouldCorrectParseJsonInStream()
    {
        //given
        StreamStub::register('json');
        StreamStub::$body = '{"name":"jonh","id":123,"ip":"127.0.0.1"}';
        ContentType::set('application/json');

        //when
        $parameters = $this->getRequestParameters('json://input');

        //then
        ArrayAssert::that($parameters)->hasSize(3);
        StreamStub::unregister();
    }

    public function getRequestParameters($stream): array
    {
        return Uri::getRequestParameters($stream);
    }

    /**
     * @test
     */
    public function shouldFailForInvalidJson()
    {
        //given
        StreamStub::register('json');
        StreamStub::$body = '{"name":"jonh","id":123,"ip":"127.0.0.1}';
        ContentType::set('application/json');

        //when
        CatchException::when($this)->getRequestParameters('json://input');

        //then
        CatchException::assertThat()->isInstanceOf(JsonDecodeException::class);
        StreamStub::unregister();
    }

    /**
     * @test
     */
    public function shouldAcceptUtfLiteralsInJson()
    {
        //given
        StreamStub::register('json');
        StreamStub::$body = '{"name":"\u0142ucja"}';
        ContentType::set('application/json');

        //when
        $parameters = $this->getRequestParameters('json://input');

        //then
        ArrayAssert::that($parameters)->containsKeyAndValue(["name" => "łucja"]);
        StreamStub::unregister();
    }

    /**
     * @test
     */
    public function shouldAcceptEmptyJson()
    {
        //given
        StreamStub::register('json');
        StreamStub::$body = '';
        ContentType::set('application/json');

        //when
        $parameters = $this->getRequestParameters('json://input');

        //then
        ArrayAssert::that($parameters)->hasSize(0);
        StreamStub::unregister();
    }

    /**
     * @test
     * @dataProvider malformedSlashes
     */
    public function shouldReplaceTwoBackSlashes(string $broken, string $good)
    {
        //given
        $this->path(Config::getPrefixSystem() . $broken);

        //when
        $path = $this->uri->getPathWithoutPrefix();

        //then
        $this->assertEquals($good, $path);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenParsePathIsNull()
    {
        //given
        $this->path(null);

        //when
        $path = $this->uri->getAction();

        //then
        $this->assertNull($path);
    }

    /**
     * @test
     * @dataProvider protocols
     */
    public function shouldReturnCorrectProtocol(string $header, mixed $value, string $expected)
    {
        //given
        $_SERVER[$header] = $value;

        //when
        $protocol = Uri::getProtocol();

        //then
        $this->assertEquals($expected, $protocol);
    }

    public function protocols(): array
    {
        return [
            ['HTTP_X_FORWARDED_PROTO', 'https', 'https://'],
            ['HTTPS', 'on', 'https://'],
            ['HTTPS', 1, 'https://'],
            ['HTTPS', 'off', 'http://']
        ];
    }

    public function malformedSlashes(): array
    {
        return [
            ['/users//index', '/users/index'],
            ['///', '/'],
            ['/actions//', '/actions']
        ];
    }

    private function path(?string $path)
    {
        Mock::when($this->pathProviderMock)->getPath()->thenReturn($path);
    }

    private function setPrivateMethodAccessible(string $testMethod): ReflectionMethod
    {
        $reflectionOfUri = new ReflectionMethod(Uri::class, $testMethod);
        $reflectionOfUri->setAccessible(true);
        return $reflectionOfUri;
    }
}
