<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Path;

class Layout
{
    /** @var View */
    public $view;

    /** @var string */
    private $renderContent;
    /** @var string */
    private $layout;

    /**
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return $this
     */
    public function unsetLayout()
    {
        $this->layout = null;
        return $this;
    }

    /**
     * @param string $renderContent
     * @return $this
     */
    public function setRenderContent($renderContent)
    {
        $this->renderContent = $renderContent;
        return $this;
    }

    /**
     * @return void
     */
    public function renderLayout()
    {
        if ($this->layout) {
            $layoutPath = Path::join(ROOT_PATH, ApplicationPaths::getLayoutPath(), $this->layout . '.phtml');
            /** @noinspection PhpIncludeInspection */
            require_once($layoutPath);
        }
    }

    /**
     * @return string
     */
    public function layoutContent()
    {
        return $this->renderContent;
    }

    /**
     * @param string $content
     * @return void
     */
    public function renderAjax($content = '')
    {
        if ($content) {
            $this->setRenderContent($content);
        }
        $this->setLayout('ajax_layout');
    }
}
