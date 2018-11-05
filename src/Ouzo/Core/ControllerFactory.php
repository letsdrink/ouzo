<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Request\RequestParameters;
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

    /**
     * @param RouteRule $routeRule
     * @param RequestParameters $requestParameters
     * @return Controller
     * @throws ControllerNotFoundException
     */
    public function createController(RouteRule $routeRule, RequestParameters $requestParameters)
    {
        $controllerName = ClassName::pathToFullyQualifiedName($routeRule->getController());
        foreach ($this->controllerNamespaces as $controllerNamespace) {
            $controller = $controllerNamespace . $controllerName . "Controller";
            if (class_exists($controller)) {
                return $this->getInstance($routeRule, $controller, $requestParameters);
            }
        }
        throw new ControllerNotFoundException('Controller [' . $controllerName . '] for URI [' . $routeRule->getUri() . '] does not exist!');
    }

    /**
     * @param RouteRule $routeRule
     * @param string $controller
     * @param RequestParameters $requestParameters
     * @return Controller
     */
    private function getInstance(RouteRule $routeRule, $controller, RequestParameters $requestParameters)
    {
        $controllerInstance = $this->injector->getInstance($controller);
        $controllerInstance->initialize($routeRule, $requestParameters);
        return $controllerInstance;
    }
}
