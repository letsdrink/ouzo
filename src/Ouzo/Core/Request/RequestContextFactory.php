<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\ControllerFactory;
use Ouzo\Injection\Factory;
use Ouzo\Stats\SessionStats;

class RequestContextFactory implements Factory
{
    /** @var RoutingService */
    private $routingService;
    /** @var RequestParameters */
    private $requestParameters;
    /** @var ControllerFactory */
    private $controllerFactory;
    /** @var SessionStats */
    private $sessionStats;

    /**
     * @Inject
     */
    public function __construct(
        RoutingService $routingService,
        RequestParameters $requestParameters,
        ControllerFactory $controllerFactory,
        SessionStats $sessionStats
    )
    {
        $this->routingService = $routingService;
        $this->requestParameters = $requestParameters;
        $this->controllerFactory = $controllerFactory;
        $this->sessionStats = $sessionStats;
    }

    /** @return RequestContext */
    public function create()
    {
        $controller = $this->routingService->getController();
        $action = $this->routingService->getAction();
        $controllerObject = $this->controllerFactory->createController($this->routingService->getRouteRule(), $this->requestParameters, $this->sessionStats);

        return new RequestContext($controller, $action, $controllerObject);
    }
}
