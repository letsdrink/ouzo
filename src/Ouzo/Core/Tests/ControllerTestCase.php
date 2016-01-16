<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests;

use Ouzo\Config;
use Ouzo\FrontController;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class ControllerTestCase extends DbTransactionalTestCase
{
    /**
     * @var FrontController
     */
    protected $frontController;

    public function __construct()
    {
        parent::__construct();

        $config = new InjectorConfig();
        $this->frontControllerBindings($config);
        $injector = new Injector($config);
        $this->frontController = $injector->getInstance('\Ouzo\FrontController');
    }

    private static function _prefixSystem()
    {
        return Config::getValue('global', 'prefix_system');
    }

    public function get($url, $data = null)
    {
        $url = $this->_appendParamsToUrl($url, $data);
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);

        $this->_initFrontController();
    }

    private function _appendParamsToUrl($url, $data)
    {
        if ($data) {
            $conjunction = $this->_urlHasParams($url) ? "&" : "?";
            return $url . $conjunction . http_build_query($data);
        }
        return $url;
    }

    private function _urlHasParams($url)
    {
        return count($this->_parseUrlParams($url)) > 0;
    }

    private function _parseUrlParams($url)
    {
        $urlComponents = parse_url($url);
        $query = Arrays::getValue($urlComponents, 'query', '');
        parse_str($query, $array);
        return $array;
    }

    private function _initFrontController()
    {
        $this->frontController->init();
    }

    public function post($url, $data)
    {
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);

        $this->_initFrontController();
    }

    public function put($url, $data)
    {
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array_merge($data, array('_method' => 'PUT'));
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);
        $this->_initFrontController();
    }

    public function patch($url)
    {
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'PATCH';
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);
        $this->_initFrontController();
    }

    public function delete($url)
    {
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'DELETE';
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);
        $this->_initFrontController();
    }

    public function assertRedirectsTo($path)
    {
        $this->assertEquals($this->_removePrefix($path), $this->_removePrefix($this->frontController->getRedirectHandler()->getLocation()));
    }

    private function _removePrefix($string)
    {
        return Strings::removePrefix($string, self::_prefixSystem());
    }

    public function assertRenders($viewName)
    {
        $statusResponse = $this->requestContext()->getCurrentControllerObject()->getStatusResponse();
        $location = $this->requestContext()->getCurrentControllerObject()->getRedirectLocation();
        if ($statusResponse != 'show') {
            $this->fail("Expected render $viewName but was $statusResponse $location");
        }
        $this->assertEquals($viewName, $this->requestContext()->getCurrentControllerObject()->view->getViewName());
    }

    public function assertAssignsModel($variable, $modelObject)
    {
        $modelVariable = $this->requestContext()->getCurrentControllerObject()->view->$variable;
        $this->assertNotNull($modelVariable);
        Assert::thatModel($modelVariable)->hasSameAttributesAs($modelObject);
    }

    public function assertDownloadsFile($file)
    {
        $this->assertEquals($file, $this->frontController->getDownloadHandler()->getFileName());
    }

    public function assertAssignsValue($variable, $value)
    {
        $this->assertNotNull($this->requestContext()->getCurrentControllerObject()->view->$variable);
        $this->assertEquals($value, $this->requestContext()->getCurrentControllerObject()->view->$variable);
    }

    public function assertRenderedContent()
    {
        return Assert::thatString($this->getActualContent());
    }

    public function assertRenderedJsonAttributeEquals($attribute, $equals)
    {
        $json = $this->getRenderedJsonAsArray();
        $this->assertEquals($equals, $json[$attribute]);
    }

    public function getAssigned($name)
    {
        return $this->requestContext()->getCurrentControllerObject()->view->$name;
    }

    public function getRenderedJsonAsArray()
    {
        return json_decode($this->getActualContent(), true);
    }

    public function assertResponseHeader($expected)
    {
        $actual = $this->getResponseHeaders();
        Assert::thatArray($actual)->contains($expected);
    }

    public function assertHasCookie($cookieAttributes)
    {
        $actual = $this->frontController->getCookiesSetter()->getCookies();
        Assert::thatArray($actual)->contains($cookieAttributes);
    }

    public function getResponseHeaders()
    {
        return $this->frontController->getHeaderSender()->getHeaders();
    }

    public function getActualContent()
    {
        return $this->frontController->getRequestContext()->getCurrentControllerObject()->layout->layoutContent();
    }

    protected function frontControllerBindings(InjectorConfig $config)
    {
        $config->bind('\Ouzo\OutputDisplayer')->toInstance(new MockOutputDisplayer());
        $config->bind('\Ouzo\HeaderSender')->toInstance(new MockHeaderSender());
        $config->bind('\Ouzo\CookiesSetter')->toInstance(new MockCookiesSetter());
        $config->bind('\Ouzo\RedirectHandler')->toInstance(new MockRedirectHandler());
        $config->bind('\Ouzo\SessionInitializer')->toInstance(new MockSessionInitializer());
        $config->bind('\Ouzo\DownloadHandler')->toInstance(new MockDownloadHandler());
    }

    protected function requestContext()
    {
        return $this->frontController->getRequestContext();
    }
}
