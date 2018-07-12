<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\ControllerFactory;
use Ouzo\Routing\Router;
use Ouzo\Uri;

class RequestContextFactory
{
    /** @var ControllerFactory */
    private $controllerFactory;

    /**
     * @Inject
     */
    public function __construct(ControllerFactory $controllerFactory)
    {
        $this->controllerFactory = $controllerFactory;
    }

    /** @return RequestContext */
    public function create()
    {
        $uri = new Uri();
        $router = new Router($uri);
        $routeRule = $router->findRoute();

        $controller = $routeRule->getController();
        $action = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();
        $controllerObject = $this->controllerFactory->createController($routeRule);

        return new RequestContext($controller, $action, $controllerObject);
    }
}
