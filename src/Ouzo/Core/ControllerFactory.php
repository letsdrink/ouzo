<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\ClassName;

class ControllerFactory
{
    /** @var array */
    private $controllerNamespaces;

    /**
     * @Inject
     * @var \Ouzo\Injection\Injector
     */
    private $injector;

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
                return $this->getInstance($routeRule, $controller);
            }
        }
        throw new ControllerNotFoundException('Controller [' . $controllerName . '] for URI [' . $routeRule->getUri() . '] does not exist!');
    }

    private function getInstance(RouteRule $routeRule, $controller)
    {
        $controllerInstance = $this->injector->getInstance($controller);
        $controllerInstance->initialize($routeRule);
        return $controllerInstance;
    }
}
