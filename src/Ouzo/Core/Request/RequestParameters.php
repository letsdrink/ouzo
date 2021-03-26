<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Uri;

class RequestParameters
{
    #[Inject]
    public function __construct(private RoutingService $routingService)
    {
    }

    public function get(string $stream = 'php://input'): array
    {
        $parameters = $this->routingService->getRouteRule()->getParameters() ?: $this->routingService->getUri()->getParams();
        $requestParameters = Uri::getRequestParameters($stream);
        return array_merge($parameters, $_POST, $_GET, $requestParameters);
    }

    public function getRoutingService(): RoutingService
    {
        return $this->routingService;
    }
}
