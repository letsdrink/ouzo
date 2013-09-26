<?php
namespace Ouzo\Routing;

use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class RouteRule
{
    private $_method;
    private $_uri;
    private $_action;
    private $_requireAction;

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

    public function matches($uri)
    {
        if ($this->_isEqualOrAnyMethod()) {
            return $this->_matches($uri);
        }
        return false;
    }

    public function hasRequiredAction($requireAction)
    {
        $this->_requireAction = $requireAction;
        if ($requireAction) {
            return (in_array($this->_method, array('GET', 'POST')) || is_array($this->_method)) && !$this->getAction();
        }
        return $requireAction;
    }

    public function isRequireAction()
    {
        return $this->_requireAction;
    }

    private function _isEqualOrAnyMethod()
    {
        return is_array($this->_method) ? in_array(Uri::getRequestType(), $this->_method) : Uri::getRequestType() == $this->_method;
    }

    private function _matches($uri)
    {
        if ($this->getUri() == $uri) {
            return true;
        }
        if (preg_match('#:\w*#', $this->getUri())) {
            $replacedUri = preg_replace('#:\w*#', '\w*', $this->getUri());
            return preg_match('#^' . $replacedUri . '$#', $uri);
        }
        if (!$this->getAction()) {
            return preg_match('#' . $this->getUri() . '#', $uri);
        }
        return false;
    }
}