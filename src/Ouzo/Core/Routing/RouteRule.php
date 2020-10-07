<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;

class RouteRule
{
    private $method;
    private $uri;
    private $controller;
    private $action;
    private $actionRequired;
    private $options;
    private $parameters = [];
    private $isResource;

    public function __construct($method, $uri, $controller, $action, $requireAction, $options = [], $isResource = false)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->controller = $controller;
        $this->action = $action;
        $this->actionRequired = $requireAction;
        $this->options = $options;
        $this->isResource = $isResource;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function isActionRequired()
    {
        return $this->actionRequired;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function hasRequiredAction()
    {
        if ($this->actionRequired) {
            return (in_array($this->method, ['GET', 'POST']) || is_array($this->method)) && !$this->action;
        }
        return $this->actionRequired;
    }

    public function matches($uri, $requestType)
    {
        if ($this->isEqualOrAnyMethod($requestType)) {
            return $this->match($uri);
        }
        return false;
    }

    private function isEqualOrAnyMethod($requestType)
    {
        return is_array($this->method) ? in_array($requestType, $this->method) : $requestType == $this->method;
    }

    private function match($uri)
    {
        preg_match('#/.+?/(.+?)(/|$)#', $uri, $matches);
        if ($this->isInExceptActions(Arrays::getValue($matches, 1, ''))) {
            return false;
        }
        $definedUri = $this->getUri();
        if ($definedUri == $uri) {
            return true;
        }
        if (strstr($definedUri, ':') !== false) {
            $replacedUri = preg_replace('#:\w*#u', '[\w.\-~_%@ \+]+', $definedUri);
            return preg_match('#^' . $replacedUri . '$#u', $uri);
        }
        if (!$this->action) {
            return preg_match('#^' . $definedUri . '/#u', $uri);
        }
        return false;
    }

    public function isInExceptActions($action)
    {
        return in_array($action, $this->getExcept());
    }

    public function getExcept()
    {
        return Arrays::getValue($this->options, 'except', []);
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

        $this->parameters = Arrays::combine($filterParameters, $filterValues);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getControllerName()
    {
        $name = implode('', $this->getControllerParts());
        return Strings::underscoreToCamelCase($name);
    }

    public function getName()
    {
        $name = Arrays::getValue($this->options, 'as', $this->prepareRuleName());
        $nameWithPath = Strings::appendSuffix($name, '_path');
        $name = lcfirst(Strings::underscoreToCamelCase($nameWithPath));
        return $this->actionRequired ? $name : '';
    }

    private function prepareRuleName()
    {
        return $this->isResource ? $this->getNameToRest() : $this->getNameToNonRest();
    }

    private function getNameToRest()
    {
        return $this->prepareResourceActionName() . $this->prepareResourceControllerName();
    }

    private function prepareResourceActionName()
    {
        $action = $this->action;
        if (in_array($action, ['fresh', 'edit'])) {
            return $action . '_';
        }
        return '';
    }

    private function prepareResourceControllerName()
    {
        $controllerParts = $this->getControllerParts();
        $result = [];

        foreach ($controllerParts as $controllerPart) {
            $parts = explode('_', $controllerPart);
            if (in_array($this->action, ['index', 'create'])) {
                $suffix = array_pop($parts);
            } else {
                $suffix = Inflector::singularize(array_pop($parts));
            }
            array_push($parts, $suffix);
            $result[] = implode('_', $parts);
        }

        rsort($result);
        return implode('_', $result);
    }

    private function getNameToNonRest()
    {
        return $this->action . '_' . $this->handleNestedResource();
    }

    private function handleNestedResource()
    {
        $parts = $this->getControllerParts();
        rsort($parts);
        return implode('_', $parts);
    }

    private function getControllerParts()
    {
        $parts = explode('\\', $this->controller);
        $parts = Arrays::map($parts, function ($part) {
            $part = Strings::removeSuffix($part, "Controller");
            return Strings::removeSuffix($part, "Application");
        });
        $controllerName = Strings::camelCaseToUnderscore(array_pop($parts));
        $parts = Arrays::map($parts, 'strtolower');
        $parts[] = $controllerName;
        return array_filter($parts);
    }
}
