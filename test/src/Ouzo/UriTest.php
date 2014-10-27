<?php
use Ouzo\Config;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\StreamStub;
use Ouzo\Uri;

class UriTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Uri
     */
    private $_uri;
    private $_pathProviderMock;

    public function setUp()
    {
        $this->_pathProviderMock = $this->getMock('\Ouzo\Uri\PathProvider', array('getPath'));
        $this->_uri = new Uri($this->_pathProviderMock);
    }

    /**
     * @test
     */
    public function shouldExtractController()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('User', $this->_uri->getController());
        $this->assertEquals('user', $this->_uri->getRawController());
    }

    /**
     * @test
     */
    public function shouldExtractAction()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('add', $this->_uri->getAction());
    }

    /**
     * @test
     */
    public function shouldGetParamValueByName()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5/name/john');

        //then
        $this->assertEquals('john', $this->_uri->getParam('name'));
        $this->assertEquals(5, $this->_uri->getParam('id'));
    }

    /**
     * @test
     */
    public function shouldGetNullValueByNonExistingNameWhenAnyParamsPassed()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5');

        //then
        $this->assertNull($this->_uri->getParam('surname'));
    }

    /**
     * @test
     */
    public function shouldGetNullValueByNonExistingNameWhenNoParamsPassed()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add');

        //then
        $this->assertNull($this->_uri->getParam('surname'));
    }

    /**
     * @test
     */
    public function shouldHandleOddNumberOfParameters()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5/name');

        //when
        $param = $this->_uri->getParam('name');

        //then
        $this->assertNull($param);
    }

    /**
     * @test
     */
    public function shouldSplitPathWithoutLimit()
    {
        //given
        $reflectionOfUri = $this->_privateMethod('_parsePath');

        //when
        $paramsExpected = array('user', 'add', 'id', '5', 'name', 'john');
        $callMethod = $reflectionOfUri->invoke(new Uri(), '/user/add/id/5/name/john');

        //then
        $this->assertEquals($paramsExpected, $callMethod);
    }

    /**
     * @test
     */
    public function shouldSplitPathWithLimit()
    {
        //given
        $reflectionOfUri = $this->_privateMethod('_parsePath');

        //when
        $paramsExpected = array('user', 'add', 'id/5/name/john');
        $callMethod = $reflectionOfUri->invoke(new Uri(), '/user/add/id/5/name/john', 3);

        //then
        $this->assertEquals($paramsExpected, $callMethod);
    }

    /**
     * @test
     */
    public function shouldGetAllParams()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5/name/john/surname/smith/');

        //when
        $params = $this->_uri->getParams();
        $paramsExpected = array('id' => 5, 'name' => 'john', 'surname' => 'smith');

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
        $this->_path(Config::getPrefixSystem() . '/user/add/id/5?param1=val1&param2=val2');

        //when
        $params = $this->_uri->getParams();
        $paramsExpected = array('id' => 5, 'param1' => 'val1', 'param2' => 'val2');

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldParseUrlWithoutParamsWhenGETDataAdded()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add?param1=val1&param2=val2&param3=t1%2Ct2%2Ct3');

        //when
        $params = $this->_uri->getParams();
        $paramsExpected = array('param1' => 'val1', 'param2' => 'val2', 'param3' => 't1,t2,t3');

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldParseUrlWhenSlashInGET()
    {
        //given
        $this->_path(Config::getPrefixSystem() . '/user/add/id/4?param1=path/to/file&param2=val2');

        //when
        $params = $this->_uri->getParams();
        $paramsExpected = array('id' => 4, 'param1' => 'path/to/file', 'param2' => 'val2');

        //then
        $this->assertEquals($paramsExpected, $params);
    }

    /**
     * @test
     */
    public function shouldCorrectParseJsonInStream()
    {
        //given
        StreamStub::register('json');
        StreamStub::$body = '{"name":"jonh","id":123,"ip":"127.0.0.1"}';

        //when
        $parameters = Uri::getRequestParameters('json://input');

        //then
        ArrayAssert::that($parameters)->hasSize(3);
        StreamStub::unregister();
    }

    /**
     * @test
     * @dataProvider malformedSlashes
     */
    public function shouldReplaceTwoBackSlashes($broken, $good)
    {
        //given
        $this->_path(Config::getPrefixSystem() . $broken);

        //when
        $path = $this->_uri->getPathWithoutPrefix();

        //then
        $this->assertEquals($good, $path);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenParsePathIsNull()
    {
        //given
        $this->_path(null);

        //when
        $path = $this->_uri->getAction();

        //then
        $this->assertNull($path);
    }

    public function malformedSlashes()
    {
        return array(
            array('/users//index', '/users/index'),
            array('///', '/'),
            array('/actions//', '/actions')
        );
    }

    private function _path($path)
    {
        $this->_pathProviderMock->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path));
    }

    private function _privateMethod($testMethod)
    {
        $reflectionOfUri = new ReflectionMethod('\Ouzo\Uri', $testMethod);
        $reflectionOfUri->setAccessible(true);
        return $reflectionOfUri;
    }
}