<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\ControllerFactory;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Factory;
use Ouzo\Stats\SessionStats;

class RequestContextFactory implements Factory
{
    /**
     * @Inject
     */
    public function __construct(
        private RoutingService $routingService,
        private RequestParameters $requestParameters,
        private ControllerFactory $controllerFactory,
        private SessionStats $sessionStats
    )
    {
    }

    public function create(): RequestContext
    {
        $controller = $this->routingService->getController();
        $action = $this->routingService->getAction();
        $controllerObject = $this->controllerFactory->createController($this->routingService->getRouteRule(), $this->requestParameters, $this->sessionStats);

        return new RequestContext($controller, $action, $controllerObject);
    }
}
