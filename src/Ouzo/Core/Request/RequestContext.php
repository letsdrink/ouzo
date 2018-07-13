<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Controller;

class RequestContext
{
    /** @var string */
    private $currentController;
    /** @var string */
    private $currentAction;
    /** @var Controller */
    private $controllerObject;

    public function __construct($controller, $action, Controller $controllerObject)
    {
        $this->currentController = $controller;
        $this->currentAction = $action;
        $this->controllerObject = $controllerObject;
    }

    /** @return string */
    public function getCurrentController()
    {
        return $this->currentController;
    }

    /** @return Controller */
    public function getCurrentControllerObject()
    {
        return $this->controllerObject;
    }

    /** @return string */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }
}
