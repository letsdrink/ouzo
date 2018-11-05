<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Routing\Router;
use Ouzo\Routing\RouteRule;
use Ouzo\Uri;

class RoutingService
{
    /** @var Uri */
    private $uri;
    /** @var RouteRule */
    private $routeRule;

    public function __construct()
    {
        $this->uri = new Uri();
        $router = new Router($this->uri);
        $this->routeRule = $router->findRoute();
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getController()
    {
        return $this->routeRule->getController();
    }

    public function getAction()
    {
        return $this->routeRule->isActionRequired() ? $this->routeRule->getAction() : $this->uri->getAction();
    }

    public function getRouteRule()
    {
        return $this->routeRule;
    }
}
