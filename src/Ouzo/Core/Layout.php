<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class Layout
{
    private array $attributes = [];

    private ?string $renderContent = null;
    private ?string $layout = null;

    public function __construct(public View $view)
    {
    }

    public function __get(string $name): mixed
    {
        return Arrays::getValue($this->attributes, $name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function setLayout(string $layout): static
    {
        $this->layout = $layout;
        return $this;
    }

    public function unsetLayout(): static
    {
        $this->layout = null;
        return $this;
    }

    public function setRenderContent(string $renderContent): static
    {
        $this->renderContent = $renderContent;
        return $this;
    }

    public function renderLayout(): void
    {
        if ($this->layout) {
            $layoutPath = Path::join(ROOT_PATH, ApplicationPaths::getLayoutPath(), "{$this->layout}.phtml");
            /** @noinspection PhpIncludeInspection */
            require_once($layoutPath);
        }
    }

    public function layoutContent(): ?string
    {
        return $this->renderContent;
    }

    public function renderAjax(?string $content = ''): void
    {
        if (!empty($content)) {
            $this->setRenderContent($content);
        }
        $this->setLayout('ajax_layout');
    }
}
