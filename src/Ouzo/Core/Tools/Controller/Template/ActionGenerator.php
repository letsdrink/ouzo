<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Controller\Template;

class ActionGenerator
{
    private $action;

    public function __construct($action)
    {
        $this->action = $action;
    }

    public function getActionName()
    {
        return $this->action;
    }

    public function getActionViewFile()
    {
        return $this->action . '.phtml';
    }

    public function templateContents()
    {
        $classStubPlaceholderReplacer = new ActionStubPlaceholderReplacer($this);
        return $classStubPlaceholderReplacer->content();
    }
}
