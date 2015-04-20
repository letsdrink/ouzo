<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;
use Ouzo\View\ViewException;
use Ouzo\View\ViewRenderer;
use Ouzo\View\ViewRendererFactory;

class View
{
    /**
     * @var ViewRenderer
     */
    private $_renderer;
    private $_renderedView;
    private $_viewName;
    private $_attributes;

    public function __construct($viewName, array $attributes = array())
    {
        if (empty($viewName)) {
            throw new ViewException('View name is empty');
        }
        $this->_viewName = $viewName;
        $this->_attributes = $attributes;

        $this->_loadHelpers();
    }

    public function render($viewName = '')
    {
        if (!empty($viewName)) {
            $this->_viewName = $viewName;
        }
        if (!$this->_renderer) {
            $this->_renderer = ViewRendererFactory::create($this->_viewName, $this->_attributes);
        }
        $this->verifyExists($this->_renderer->getViewPath(), $this->_viewName);
        $this->_renderedView = $this->_renderer->render();
        return $this->_renderedView;
    }

    private function verifyExists($viewPath, $viewName)
    {
        if (!Files::exists($viewPath)) {
            throw new ViewException('No view found [' . $viewName . '] at: ' . $viewPath);
        }
    }

    public function getRenderedView()
    {
        return !empty($this->_renderedView) ? $this->_renderedView : null;
    }

    public function getViewName()
    {
        return $this->_viewName;
    }

    private function _loadHelpers()
    {
        $viewHelperPath = Path::join('Helper', 'ViewHelper.php');
        $appHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'ApplicationHelper.php');
        $formHelperPath = Path::join('Helper', 'FormHelper.php');
        $urlHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'UrlHelper.php');

        $this->_requireOnce($viewHelperPath);
        Files::loadIfExists($appHelperPath);
        $this->_requireOnce($formHelperPath);
        Files::loadIfExists($urlHelperPath);
    }

    private function _requireOnce($path)
    {
        /** @noinspection PhpIncludeInspection */
        require_once($path);
    }

    /**
     * @return ViewRenderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    function __get($name)
    {
        return $this->_attributes[$name];
    }
}
