<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

class RequestContext
{
    private $currentController;
    private $currentAction;
    private $controllerObject;

    public function __construct($controller, $action, $controllerObject)
    {
        $this->currentController = $controller;
        $this->currentAction = $action;
        $this->controllerObject = $controllerObject;
    }

    public function getCurrentController()
    {
        return $this->currentController;
    }

    public function getCurrentControllerObject()
    {
        return $this->controllerObject;
    }

    public function getCurrentAction()
    {
        return $this->currentAction;
    }
}
