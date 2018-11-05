<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\ControllerFactory;

class RequestContextFactory
{
    /** @var RoutingService */
    private $routingService;
    /** @var RequestParameters */
    private $requestParameters;
    /** @var ControllerFactory */
    private $controllerFactory;

    /**
     * @Inject
     */
    public function __construct(
        RoutingService $routingService,
        RequestParameters $requestParameters,
        ControllerFactory $controllerFactory
    )
    {
        $this->routingService = $routingService;
        $this->requestParameters = $requestParameters;
        $this->controllerFactory = $controllerFactory;
    }

    /** @return RequestContext */
    public function create()
    {
        $controller = $this->routingService->getController();
        $action = $this->routingService->getAction();
        $controllerObject = $this->controllerFactory->createController($this->routingService->getRouteRule(), $this->requestParameters);

        return new RequestContext($controller, $action, $controllerObject);
    }
}
