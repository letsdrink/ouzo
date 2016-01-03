<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Request;

class RequestContext
{
    private $currentController;
    private $currentControllerObject;

    public function getCurrentController()
    {
        return $this->currentController;
    }

    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
    }

    public function getCurrentControllerObject()
    {
        return $this->currentControllerObject;
    }

    public function setCurrentControllerObject($currentControllerObject)
    {
        $this->currentControllerObject = $currentControllerObject;
    }
}
