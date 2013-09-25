<?php
namespace Ouzo\Routing;

use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class RouteRule
{
    private $_method;
    private $_uri;
    private $_action;

    public function __construct($method, $uri, $action)
    {
        $this->_method = $method;
        $this->_uri = $uri;
        $this->_action = $action;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    public function getController()
    {
        $elements = explode('#', $this->_action);
        return $elements[0];
    }

    public function getAction()
    {
        $elements = explode('#', $this->_action);
        return Arrays::getValue($elements, 1);
    }

    public function isMatching($uri)
    {
        if (Uri::getRequestType() == $this->_method) {
            return $this->_checkIsMatching($uri);
        }
        return false;
    }

    private function _checkIsMatching($uri)
    {
        if ($this->getUri() == $uri) {
            return true;
        } else if (preg_match('#:\w*#', $this->getUri())) {
            $replacedUri = preg_replace('#:\w*#', '\w*', $this->getUri());
            return preg_match('#^' . $replacedUri . '$#', $uri);
        }
        return false;
    }
}