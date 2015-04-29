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
    private $controllerNamespaces;

    public function __construct()
    {
        $this->controllerNamespaces = AutoloadNamespaces::getControllerNamespace();
    }

    public function createController(RouteRule $routeRule)
    {
        $controllerName = ClassName::pathToFullyQualifiedName($routeRule->getController());
        foreach ($this->controllerNamespaces as $controllerNamespace) {
            $controller = $controllerNamespace . $controllerName . "Controller";
            if (class_exists($controller)) {
                return new $controller($routeRule);
            }
        }
        throw new ControllerNotFoundException('Controller does not exist');
    }
}

class ControllerNotFoundException extends Exception
{
}
