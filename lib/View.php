<?php
namespace Thulium;

class View
{
    private $_viewName = '';

    protected $_renderedView = null;

    public function __construct($viewName, array $attributes = array())
    {
        if (empty($viewName))
            throw new ViewException('Type view name');
        else
            $this->_viewName = $viewName;

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'thulium' . DIRECTORY_SEPARATOR . 'ViewHelper.php');
        require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'ApplicationHelper.php');
        require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'FormHelper.php');
        require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'UrlHelper.php');
    }

    public function render($viewName = '')
    {
        if (!empty($viewName)) {
            $this->_viewName = $viewName;
        }

        ob_start();
        $this->_loadFirstExisting(array($this->_helperCustomPath(), $this->_helperApplicationPath()), true);

        $viewLoaded = $this->_loadFirstExisting(array($this->_viewCustomPath(), $this->_viewApplicationPath()));
        if (!$viewLoaded) {
            throw new ViewException('No view found [' . $this->_viewName . ']');
        }

        $view = ob_get_contents();
        ob_end_clean();

        $this->_renderedView = $view;

        return $view;
    }

    private function _loadFirstExisting(array $files, $loadOnce = false)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $this->requireWithoutInspection($file, $loadOnce);
                return true;
            }
        }
        return false;
    }

    private function requireWithoutInspection($name, $loadOnce)
    {
        if ($loadOnce) {
            require_once($name);
        } else {
            /** @noinspection PhpIncludeInspection */
            require($name);
        }
    }

    public function getRenderedView()
    {
        return (!empty($this->_renderedView) ? $this->_renderedView : null);
    }

    public function getViewName()
    {
        return $this->_viewName;
    }

    private function _viewApplicationPath()
    {
        return ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_viewName . '.phtml';
    }

    private function _viewCustomPath()
    {
        return ROOT_PATH . 'custom' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_viewName . '.phtml';
    }

    private function _helperApplicationPath()
    {
        return ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_viewName . '.helper.php';
    }

    private function _helperCustomPath()
    {
        return ROOT_PATH . 'custom' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_viewName . '.helper.php';
    }
}

class ViewException extends \Exception
{
}