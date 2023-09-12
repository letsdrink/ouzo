<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class PhtmlRenderer implements ViewRenderer
{
    const EXTENSION = '.phtml';

    private string $viewPath;

    public function __construct(
        private string $viewName,
        private array $attributes,
        private ViewPathProvider $viewPathProvider
    )
    {
        $this->viewPath = $this->viewPathProvider->getViewPath($this->viewName, self::EXTENSION);
    }

    public function render(): string
    {
        ob_start();
        try {
            $this->loadViewHelper();
            $this->loadView();
        } catch (Exception $e) {
            ob_end_flush();
            throw $e;
        }
        $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }

    private function loadViewHelper(): void
    {
        $helperPath = Strings::removeSuffix($this->viewPath, self::EXTENSION) . '.helper.php';
        Files::loadIfExists($helperPath);
    }

    private function loadView(): void
    {
        /** @noinspection PhpIncludeInspection */
        require($this->viewPath);
    }

    public function __get(string $name)
    {
        return Arrays::getValue($this->attributes, $name);
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function getViewPath(): string
    {
        return $this->viewPath;
    }
}
