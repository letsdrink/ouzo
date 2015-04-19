<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\ClassName;

class ControllerFactory
{
    public function __construct()
    {
        $this->controllerNamespace = AutoloadNamespaces::getControllerNamespace();
    }

    public function createController(RouteRule $routeRule)
    {
        $controller = $routeRule->getController();
        $controllerName = ClassName::pathToFullyQualifiedName($controller);
        $controller = $this->controllerNamespace . $controllerName . "Controller";

        $this->_validateControllerExists($controller);

        return new $controller($routeRule);
    }

    private function _validateControllerExists($controller)
    {
        if (!class_exists($controller)) {
            throw new ControllerNotFoundException('Controller does not exist: ' . $controller);
        }
    }
}

class ControllerNotFoundException extends Exception
{
}
