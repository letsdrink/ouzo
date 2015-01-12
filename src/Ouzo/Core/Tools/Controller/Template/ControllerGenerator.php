<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Controller\Template;

use Ouzo\AutoloadNamespaces;
use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class ControllerGenerator
{
    private $controller;
    private $controllerPath;

    public function __construct($controller, $controllerPath = null)
    {
        $this->controller = $controller;
        $this->controllerPath = $controllerPath;
    }

    public function getClassName()
    {
        $class = Strings::underscoreToCamelCase($this->controller);
        if (Strings::endsWith($class, 'Controller')) {
            return $class;
        }
        return Strings::appendSuffix($class, 'Controller');
    }

    public function getClassNamespace()
    {
        return rtrim(AutoloadNamespaces::getControllerNamespace(), '\\');
    }

    public function isControllerExists()
    {
        return Files::exists($this->getControllerPath());
    }

    public function getControllerPath()
    {
        return $this->controllerPath ?: ClassPathResolver::forClassAndNamespace($this->getClassName(), $this->getClassNamespace())->getClassFileName();
    }

    public function templateContents()
    {
        $classStubPlaceholderReplacer = new ControllerClassStubPlaceholderReplacer($this);
        return $classStubPlaceholderReplacer->content();
    }

    public function saveController()
    {
        $path = $this->getControllerPath();
        $this->preparePaths(dirname($path));
        file_put_contents($path, $this->templateContents());
    }

    private function preparePaths($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function getControllerContents()
    {
        $controllerPath = $this->getControllerPath();
        if (Files::exists($controllerPath)) {
            return file_get_contents($controllerPath);
        }
        return '';
    }

    public function appendAction(ActionGenerator $actionGenerator = null)
    {
        if ($actionGenerator) {
            if ($this->isActionExists($actionGenerator->getActionName())) {
                return false;
            }
            $actionAppender = new ActionAppender($actionGenerator);
            return $actionAppender->toController($this)->append();
        }
        return false;
    }

    public function isActionExists($actionName)
    {
        return Strings::contains($this->getControllerContents(), 'function ' . $actionName);
    }
}
