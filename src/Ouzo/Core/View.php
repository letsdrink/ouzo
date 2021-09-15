<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;
use Ouzo\View\ViewException;
use Ouzo\View\ViewPathProvider;
use Ouzo\View\ViewPathProviderFactory;
use Ouzo\View\ViewRenderer;
use Ouzo\View\ViewRendererFactory;

class View
{
    private ?ViewRenderer $renderer = null;
    private string $renderedView;
    private string $viewName;
    private array $attributes;
    private ViewPathProvider $viewPathProvider;

    public function __construct(?string $viewName, array $attributes = [], ?ViewPathProvider $viewPathProvider = null)
    {
        if (empty($viewName)) {
            throw new ViewException('View name is empty');
        }
        $this->viewName = $viewName;
        $this->attributes = $attributes;
        $this->viewPathProvider = $viewPathProvider ?? ViewPathProviderFactory::create();

        $this->loadHelpers();
    }

    public function render(?string $viewName = ''): string
    {
        if (!empty($viewName)) {
            $this->viewName = $viewName;
        }
        if (!$this->renderer) {
            $this->renderer = ViewRendererFactory::create($this->viewName, $this->attributes, $this->viewPathProvider);
        }
        $this->verifyExists($this->renderer->getViewPath(), $this->viewName);
        $this->renderedView = $this->renderer->render();
        return $this->renderedView;
    }

    private function verifyExists(string $viewPath, string $viewName): void
    {
        if (!Files::exists($viewPath)) {
            throw new ViewException("No view found [{$viewName}] at: {$viewPath}");
        }
    }

    public function getRenderedView(): ?string
    {
        return !empty($this->renderedView) ? $this->renderedView : null;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    private function loadHelpers(): void
    {
        $viewHelperPath = Path::join('Helper', 'ViewHelper.php');
        $appHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'ApplicationHelper.php');
        $formHelperPath = Path::join('Helper', 'FormHelper.php');
        $urlHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'UrlHelper.php');

        $this->requireOnce($viewHelperPath);
        Files::loadIfExists($appHelperPath);
        $this->requireOnce($formHelperPath);
        Files::loadIfExists($urlHelperPath);
    }

    private function requireOnce($path): void
    {
        /** @noinspection PhpIncludeInspection */
        require_once($path);
    }

    public function getRenderer(): ViewRenderer
    {
        return $this->renderer;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name];
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
