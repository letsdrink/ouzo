<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\View;

use Exception;

class TwigRenderer implements ViewRenderer
{

    private $_viewName;

    function __construct($viewName)
    {
        $this->_viewName = $viewName;
    }

    public function render()
    {
        throw new Exception('Not yet implemented');
    }
}
