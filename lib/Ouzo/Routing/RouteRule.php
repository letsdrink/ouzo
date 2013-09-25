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
            return $this->getUri() == $uri;
        }
        return false;
    }
}