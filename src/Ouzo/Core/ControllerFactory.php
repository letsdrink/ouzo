<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use LogicException;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Injector;
use Ouzo\Request\RequestParameters;
use Ouzo\Routing\RouteRule;
use Ouzo\Stats\SessionStats;

class ControllerFactory
{
    #[Inject]
    private Injector $injector;

    public function createController(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats): Controller
    {
        $controller = $routeRule->getController();
        if (!class_exists($controller)) {
            throw new ControllerNotFoundException("Controller [{$controller}] for URI [{$routeRule->getUri()}] does not exist!");
        }
        if (!is_subclass_of($controller, Controller::class)) {
            throw new LogicException("{$controller} is not a subclass of Controller");
        }
        return $this->getInstance($routeRule, $controller, $requestParameters, $sessionStats);
    }

    private function getInstance(RouteRule $routeRule, string $controller, RequestParameters $requestParameters, SessionStats $sessionStats): Controller
    {
        /** @var Controller $controllerInstance */
        $controllerInstance = $this->injector->getInstance($controller);
        $controllerInstance->initialize($routeRule, $requestParameters, $sessionStats);
        return $controllerInstance;
    }
}
