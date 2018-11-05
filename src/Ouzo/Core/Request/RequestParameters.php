<?php

namespace Ouzo\Request;

use Ouzo\Uri;

class RequestParameters
{
    /** @var RoutingService */
    private $routingService;

    /**
     * @Inject
     */
    public function __construct(RoutingService $routingService)
    {
        $this->routingService = $routingService;
    }

    public function get($stream = 'php://input')
    {
        $parameters = $this->routingService->getRouteRule()->getParameters() ? $this->routingService->getRouteRule()->getParameters() : $this->routingService->getUri()->getParams();
        $requestParameters = Uri::getRequestParameters($stream);
        return array_merge($parameters, $_POST, $_GET, $requestParameters);
    }
}
