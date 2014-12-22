<?php
namespace Ouzo\Tests;

use Ouzo\Config;
use Ouzo\FrontController;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class ControllerTestCase extends DbTransactionalTestCase
{
    protected $_frontController;

    private $_redirectHandler;
    private $_sessionInitializer;
    private $_downloadHandler;

    public function __construct()
    {
        parent::__construct();

        $this->_redirectHandler = new MockRedirectHandler();
        $this->_sessionInitializer = new MockSessionInitializer();
        $this->_downloadHandler = new MockDownloadHandler();

        $this->_frontController = new FrontController();
        $this->_frontController->redirectHandler = $this->_redirectHandler;
        $this->_frontController->sessionInitializer = $this->_sessionInitializer;
        $this->_frontController->downloadHandler = $this->_downloadHandler;
        $this->_frontController->outputDisplayer = new MockOutputDisplayer();
        $this->_frontController->headerSender = new MockHeaderSender();
    }

    private static function _prefixSystem()
    {
        return Config::getValue('global', 'prefix_system');
    }

    public function get($url)
    {
        $_SERVER['REQUEST_URI'] = self::_prefixSystem() . $url;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $this->_parseUrlParams($_SERVER['REQUEST_URI']);

        $this->_initFrontController();
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
        $this->_frontController->init();
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
        $this->assertEquals($this->_removePrefix($path), $this->_removePrefix($this->_redirectHandler->getLocation()));
    }

    private function _removePrefix($string)
    {
        return Strings::removePrefix($string, self::_prefixSystem());
    }

    public function assertRenders($viewName)
    {
        $statusResponse = RequestContext::getCurrentControllerObject()->getStatusResponse();
        $location = RequestContext::getCurrentControllerObject()->getRedirectLocation();
        if ($statusResponse != 'show') {
            $this->fail("Expected render $viewName but was $statusResponse $location");
        }
        $this->assertEquals($viewName, RequestContext::getCurrentControllerObject()->view->getViewName());
    }

    public function assertAssignsModel($variable, $modelObject)
    {
        $modelVariable = RequestContext::getCurrentControllerObject()->view->$variable;
        $this->assertNotNull($modelVariable);
        Assert::thatModel($modelVariable)->hasSameAttributesAs($modelObject);
    }

    public function assertDownloadsFile($file)
    {
        $this->assertEquals($file, $this->_downloadHandler->getFileName());
    }

    public function assertAssignsValue($variable, $value)
    {
        $this->assertNotNull(RequestContext::getCurrentControllerObject()->view->$variable);
        $this->assertEquals($value, RequestContext::getCurrentControllerObject()->view->$variable);
    }

    public function assertRenderedContent()
    {
        return Assert::thatString(RequestContext::getCurrentControllerObject()->layout->layoutContent());
    }

    public function assertRenderedJsonAttributeEquals($attribute, $equals)
    {
        $json = $this->getRenderedJsonAsArray();
        $this->assertEquals($equals, $json[$attribute]);
    }

    public function getAssigned($name)
    {
        return RequestContext::getCurrentControllerObject()->view->$name;
    }

    public function getRenderedJsonAsArray()
    {
        return json_decode(RequestContext::getCurrentControllerObject()->layout->layoutContent(), true);
    }

    public function assertResponseHeader($expected)
    {
        $actual = $this->getResponseHeaders();
        Assert::thatArray($actual)->contains($expected);
    }

    public function getResponseHeaders()
    {
        return $this->_frontController->headerSender->getHeaders();
    }
}
