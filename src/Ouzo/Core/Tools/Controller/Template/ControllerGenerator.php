<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Controller\Template;

use Ouzo\AutoloadNamespaces;
use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class ControllerGenerator
{
    public function __construct(private string $controller, private ?string $controllerPath = null)
    {
    }

    public function getClassName(): string
    {
        $class = Strings::underscoreToCamelCase($this->controller);
        if (Strings::endsWith($class, 'Controller')) {
            return $class;
        }
        return Strings::appendSuffix($class, 'Controller');
    }

    public function getClassNamespace(): string
    {
        $controllerNamespaces = AutoloadNamespaces::getControllerNamespace();
        return rtrim($controllerNamespaces[0], '\\');
    }

    public function isControllerExists(): bool
    {
        return Files::exists($this->getControllerPath());
    }

    public function getControllerPath(): string
    {
        return $this->controllerPath ?: ClassPathResolver::forClassAndNamespace($this->getClassName(), $this->getClassNamespace())->getClassFileName();
    }

    public function templateContents(): string
    {
        $replacer = new ControllerClassStubPlaceholderReplacer($this);
        return $replacer->content();
    }

    public function saveController(): void
    {
        $path = $this->getControllerPath();
        $this->preparePaths(dirname($path));
        file_put_contents($path, $this->templateContents());
    }

    private function preparePaths(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function getControllerContents(): string
    {
        $controllerPath = $this->getControllerPath();
        if (Files::exists($controllerPath)) {
            return file_get_contents($controllerPath);
        }
        return '';
    }

    public function appendAction(ActionGenerator $actionGenerator = null): bool
    {
        if ($actionGenerator) {
            if ($this->isActionExists($actionGenerator->getActionName())) {
                return false;
            }
            $appender = new ActionAppender($actionGenerator);
            return $appender->toController($this)->append();
        }
        return false;
    }

    public function isActionExists($actionName): bool
    {
        return Strings::contains($this->getControllerContents(), "function {$actionName}");
    }
}
