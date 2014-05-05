<?php
namespace Ouzo\Routing;

use Ouzo\Uri;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;

class RouteRule
{
    private $_method;
    private $_uri;
    private $_action;
    private $_actionRequired;
    private $_options;
    private $_parameters = array();
    private $_isResource;

    public function __construct($method, $uri, $action, $requireAction, $options = array(), $isResource = false)
    {
        $this->_method = $method;
        $this->_uri = $uri;
        $this->_action = $action;
        $this->_actionRequired = $requireAction;
        $this->_options = $options;
        $this->_isResource = $isResource;
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
        return Arrays::first($elements);
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

    public function hasRequiredAction()
    {
        if ($this->_actionRequired) {
            return (in_array($this->_method, array('GET', 'POST')) || is_array($this->_method)) && !$this->getAction();
        }
        return $this->_actionRequired;
    }

    public function isActionRequired()
    {
        return $this->_actionRequired;
    }

    public function getExcept()
    {
        return Arrays::getValue($this->_options, 'except', array());
    }

    public function isInExceptActions($action)
    {
        return in_array($action, $this->getExcept());
    }

    private function _isEqualOrAnyMethod()
    {
        return is_array($this->_method) ? in_array(Uri::getRequestType(), $this->_method) : Uri::getRequestType() == $this->_method;
    }

    private function _matches($uri)
    {
        preg_match('#/.+?/(.+?)(/|$)#', $uri, $matches);
        if ($this->isInExceptActions(Arrays::getValue($matches, 1, ''))) {
            return false;
        }
        if ($this->getUri() == $uri) {
            return true;
        }
        if (preg_match('#:\w*#', $this->getUri())) {
            $replacedUri = preg_replace('#:\w*#', '[A-Za-z0-9.\-~_]+', $this->getUri());
            return preg_match('#^' . $replacedUri . '$#', $uri);
        }
        if (!$this->getAction()) {
            return preg_match('#' . $this->getUri() . '/#', $uri);
        }
        return false;
    }

    public function setParameters($uri)
    {
        $ruleUri = explode('/', $this->getUri());
        $requestUri = explode('/', $uri);

        $filterParameters = FluentArray::from($ruleUri)
            ->filter(function ($parameter) {
                return preg_match('#:\w+#', $parameter);
            })
            ->map(function ($parameter) {
                return str_replace(':', '', $parameter);
            })
            ->toArray();

        $filterValues = array_intersect_key($requestUri, $filterParameters);

        $this->_parameters = Arrays::combine($filterParameters, $filterValues);
    }

    public function getParameters()
    {
        return $this->_parameters;
    }

    public function getName()
    {
        $name = Arrays::getValue($this->_options, 'as', $this->_prepareRuleName());
        $nameWithPath = Strings::appendSuffix($name, '_path');
        $name = lcfirst(Strings::underscoreToCamelCase($nameWithPath));
        return $this->_actionRequired ? $name : '';
    }

    private function _prepareRuleName()
    {
        return $this->_isResource ? $this->_getNameToRest() : $this->_getNameToNonRest();
    }

    private function _getNameToRest()
    {
        return $this->_prepareResourceActionName() . $this->_prepareResourceControllerName();
    }

    private function _prepareResourceActionName()
    {
        if (in_array($this->getAction(), array('fresh', 'edit'))) {
            return $this->getAction() . '_';
        }
        return '';
    }

    private function _prepareResourceControllerName()
    {
        $parts = explode('_', $this->getController());
        if (in_array($this->getAction(), array('index', 'create'))) {
            $suffix = array_pop($parts);
        } else {
            $suffix = Inflector::singularize(array_pop($parts));
        }
        $parts[] = $suffix;
        return implode('_', $parts);
    }

    private function _getNameToNonRest()
    {
        return $this->getAction() . '_' . $this->_handleNestedResource();
    }

    private function _handleNestedResource()
    {
        $controller = $this->getController();
        $parts = explode('/', $controller);
        rsort($parts);
        return implode('_', $parts);
    }
}