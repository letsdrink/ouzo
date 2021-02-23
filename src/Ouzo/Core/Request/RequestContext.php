<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Controller;

class RequestContext
{
    public function __construct(
        private string $controller,
        private string $action,
        private Controller $controllerObject
    )
    {
    }

    public function getCurrentController(): string
    {
        return $this->controller;
    }

    public function getCurrentControllerObject(): Controller
    {
        return $this->controllerObject;
    }

    public function getCurrentAction(): string
    {
        return $this->action;
    }
}
