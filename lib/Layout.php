<?php
namespace Thulium;

class Layout
{
    private $_renderContent = null;
    private $_layout = null;

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
            $layoutPath = ROOT_PATH . DIRECTORY_SEPARATOR . 'application/layout/' . $this->_layout . '.phtml';
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