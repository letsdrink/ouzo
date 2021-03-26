<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\Router;
use Ouzo\Routing\RouteRule;
use Ouzo\Uri;

class RoutingService
{
    private RouteRule $routeRule;

    #[Inject]
    public function __construct(private Uri $uri)
    {
        $router = new Router($this->uri);
        $this->routeRule = $router->findRoute();
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getController(): string
    {
        return $this->routeRule->getController();
    }

    public function getAction(): string
    {
        return $this->routeRule->isRequiredAction() ? $this->routeRule->getAction() : $this->uri->getAction();
    }

    public function getRouteRule(): RouteRule
    {
        return $this->routeRule;
    }
}
