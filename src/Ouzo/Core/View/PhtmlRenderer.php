<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Exception;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class PhtmlRenderer implements ViewRenderer
{
    const EXTENSION = '.phtml';

    private string $viewPath;

    public function __construct(private string $viewName, private array $attributes)
    {
        $this->viewPath = ViewPathProviderFactory::create()->getViewPath($this->viewName, self::EXTENSION);
    }

    public function render(): string
    {
        $this->saveAttributes();
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
        $helperPath = Strings::removeSuffix($this->viewPath, self::EXTENSION) . ".helper.php";
        Files::loadIfExists($helperPath);
    }

    private function loadView(): void
    {
        /** @noinspection PhpIncludeInspection */
        require($this->viewPath);
    }

    private function saveAttributes(): void
    {
        foreach ($this->attributes as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getViewPath(): string
    {
        return $this->viewPath;
    }
}
