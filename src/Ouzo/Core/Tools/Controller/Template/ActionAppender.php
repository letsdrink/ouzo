<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Controller\Template;

use Ouzo\Utilities\Path;

class ActionAppender
{
    private ?ControllerGenerator $controllerGenerator = null;
    private ?ViewGenerator $viewGenerator = null;

    public function __construct(private ActionGenerator $actionGenerator)
    {
    }

    public function toController(ControllerGenerator $controllerGenerator): static
    {
        $this->controllerGenerator = $controllerGenerator;
        return $this;
    }

    public function toView(ViewGenerator $viewGenerator): static
    {
        $this->viewGenerator = $viewGenerator;
        return $this;
    }

    public function append(): bool
    {
        if ($this->controllerGenerator) {
            $controllerPath = $this->controllerGenerator->getControllerPath();
            $controllerContents = $this->controllerGenerator->getControllerContents();
            $actionContents = $this->actionGenerator->templateContents();
            $controllerContents = preg_replace('/}\\s*$/', $actionContents . PHP_EOL . '}' . PHP_EOL, $controllerContents);
            file_put_contents($controllerPath, $controllerContents);
        }
        if ($this->viewGenerator) {
            file_put_contents(Path::join($this->viewGenerator->getViewPath(), $this->actionGenerator->getActionViewFile()), PHP_EOL);
        }
        return true;
    }
}
