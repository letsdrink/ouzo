<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Controller\Template;

class ActionGenerator
{
    public function __construct(private string $action)
    {
    }

    public function getActionName(): string
    {
        return $this->action;
    }

    public function getActionViewFile(): string
    {
        return "{$this->action}.phtml";
    }

    public function templateContents(): string
    {
        $replacer = new ActionStubPlaceholderReplacer($this);
        return $replacer->content();
    }
}
