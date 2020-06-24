<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use LogicException;
use Ouzo\Request\RequestParameters;
use Ouzo\Routing\RouteRule;
use Ouzo\Stats\SessionStats;

class ControllerFactory
{
    /**
     * @Inject
     * @var \Ouzo\Injection\Injector
     */
    private $injector;

    /**
     * @param RouteRule $routeRule
     * @param RequestParameters $requestParameters
     * @param SessionStats $sessionStats
     * @return Controller
     * @throws ControllerNotFoundException
     */
    public function createController(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats)
    {
        $controller = $routeRule->getController();
        if (!class_exists($controller)) {
            throw new ControllerNotFoundException('Controller [' . $controller . '] for URI [' . $routeRule->getUri() . '] does not exist!');
        }
        if (!is_subclass_of($controller, Controller::class)) {
            throw new LogicException($controller . ' is not a subclass of ' . Controller::class);
        }
        return $this->getInstance($routeRule, $controller, $requestParameters, $sessionStats);
    }

    /**
     * @param RouteRule $routeRule
     * @param string $controller
     * @param RequestParameters $requestParameters
     * @param SessionStats $sessionStats
     * @return Controller
     */
    private function getInstance(RouteRule $routeRule, $controller, RequestParameters $requestParameters, SessionStats $sessionStats)
    {
        /** @var Controller $controllerInstance */
        $controllerInstance = $this->injector->getInstance($controller);
        $controllerInstance->initialize($routeRule, $requestParameters, $sessionStats);
        return $controllerInstance;
    }
}
