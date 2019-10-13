<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\Config;
use Ouzo\CookiesSetter;
use Ouzo\DownloadHandler;
use Ouzo\FrontController;
use Ouzo\HeaderSender;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\OutputDisplayer;
use Ouzo\RedirectHandler;
use Ouzo\Request\RequestHeaders;
use Ouzo\Uri\PathProvider;
use Ouzo\Uri\PathProviderInterface;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;

class ControllerTestCase extends DbTransactionalTestCase
{
    /** @var InjectorConfig */
    protected $injectorConfig;
    /** @var FrontController */
    protected $frontController;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $mockSessionInitializer = new MockSessionInitializer();
        $mockSessionInitializer->startSession();
        $this->injectorConfig = new InjectorConfig();
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        $_REQUEST = [];
        $_ENV = [];
        $_FILES = [];
        $_COOKIE = [];
        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['REDIRECT_QUERY_STRING']);
        unset($_SERVER['HTTP_ACCEPT']);
        unset($_SERVER['HTTP_CLIENT_IP']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['REMOTE_ADDR']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        FrontController::$requestId = null;
        RequestHeaders::clearCache();
    }

    private static function prefixSystem()
    {
        return Config::getValue('global', 'prefix_system');
    }

    public function get($url, $data = null)
    {
        $url = $this->appendParamsToUrl($url, $data);
        $_SERVER['REQUEST_URI'] = self::prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $this->parseUrlParams($_SERVER['REQUEST_URI']);

        $this->initFrontController();
    }

    private function appendParamsToUrl($url, $data)
    {
        if ($data) {
            $conjunction = $this->urlHasParams($url) ? "&" : "?";
            return $url . $conjunction . http_build_query($data);
        }
        return $url;
    }

    private function urlHasParams($url)
    {
        return count($this->parseUrlParams($url)) > 0;
    }

    private function parseUrlParams($url)
    {
        $urlComponents = parse_url($url);
        $query = Arrays::getValue($urlComponents, 'query', '');
        parse_str($query, $array);
        return $array;
    }

    protected function initFrontController()
    {
        $this->frontControllerBindings($this->injectorConfig);
        $injector = new Injector($this->injectorConfig);
        $this->frontController = $injector->getInstance(FrontController::class);

        $this->frontController->init();
    }

    public function post($url, $data)
    {
        $_SERVER['REQUEST_URI'] = self::prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
        $_GET = $this->parseUrlParams($_SERVER['REQUEST_URI']);

        $this->initFrontController();
    }

    public function put($url, $data)
    {
        $_SERVER['REQUEST_URI'] = self::prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array_merge($data, ['_method' => 'PUT']);
        $_GET = $this->parseUrlParams($_SERVER['REQUEST_URI']);
        $this->initFrontController();
    }

    public function patch($url)
    {
        $_SERVER['REQUEST_URI'] = self::prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'PATCH';
        $_GET = $this->parseUrlParams($_SERVER['REQUEST_URI']);
        $this->initFrontController();
    }

    public function delete($url)
    {
        $_SERVER['REQUEST_URI'] = self::prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'DELETE';
        $_GET = $this->parseUrlParams($_SERVER['REQUEST_URI']);
        $this->initFrontController();
    }

    public function assertRedirectsTo($path)
    {
        $expected = $this->removePrefix($path);
        $actual = $this->removePrefix($this->frontController->getRequestExecutor()->getRedirectHandler()->getLocation());
        $this->assertEquals($expected, $actual);
    }

    private function removePrefix($string)
    {
        return Strings::removePrefix($string, self::prefixSystem());
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
        $this->assertEquals($file, $this->frontController->getRequestExecutor()->getDownloadHandler()->getFileName());
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
        return Json::decode($this->getActualContent(), true);
    }

    public function assertResponseHeader($expected)
    {
        $actual = $this->getResponseHeaders();
        Assert::thatArray($actual)->contains($expected);
    }

    public function assertHasCookie($cookieAttributes)
    {
        $actual = $this->frontController->getRequestExecutor()->getCookiesSetter()->getCookies();
        Assert::thatArray($actual)->contains($cookieAttributes);
    }

    public function getResponseHeaders()
    {
        return $this->frontController->getRequestExecutor()->getHeaderSender()->getHeaders();
    }

    public function getActualContent()
    {
        return $this->frontController->getRequestContext()->getCurrentControllerObject()->layout->layoutContent();
    }

    protected function frontControllerBindings(InjectorConfig $config)
    {
        $config->bind(OutputDisplayer::class)->toInstance(new MockOutputDisplayer());
        $config->bind(HeaderSender::class)->toInstance(new MockHeaderSender());
        $config->bind(CookiesSetter::class)->toInstance(new MockCookiesSetter());
        $config->bind(RedirectHandler::class)->toInstance(new MockRedirectHandler());
        $config->bind(DownloadHandler::class)->toInstance(new MockDownloadHandler());
        $config->bind(PathProviderInterface::class)->toInstance(new PathProvider());
    }

    protected function requestContext()
    {
        return $this->frontController->getRequestContext();
    }
}
