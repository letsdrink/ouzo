<?php
namespace Ouzo;

use Ouzo\Utilities\Path;

class Layout
{
    private $_renderContent;
    private $_layout;

    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    public function unsetLayout()
    {
        $this->_layout = null;
        return $this;
    }

    public function setRenderContent($renderContent)
    {
        $this->_renderContent = $renderContent;
        return $this;
    }

    public function renderLayout()
    {
        if ($this->_layout) {
            $layoutPath = Path::join(ROOT_PATH, 'application', 'layout', $this->_layout . '.phtml');
            /** @noinspection PhpIncludeInspection */
            require_once($layoutPath);
        }
    }

    public function layoutContent()
    {
        return $this->_renderContent;
    }

    public function renderAjax($content = '')
    {
        if ($content) {
            $this->setRenderContent($content);
        }
        $this->setLayout('ajax_layout');
    }
}