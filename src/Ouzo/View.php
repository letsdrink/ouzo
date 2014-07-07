<?php
namespace Ouzo;

use Exception;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class View
{
    private $_viewName = '';

    protected $_renderedView = null;

    public function __construct($viewName, array $attributes = array())
    {
        if (empty($viewName)) {
            throw new ViewException('View name is empty');
        }
        $this->_viewName = $viewName;

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        $this->_loadHelpers();
    }

    public function render($viewName = '')
    {
        if (!empty($viewName)) {
            $this->_viewName = $viewName;
        }
        $this->_renderedView = $this->_renderUsingOutputBuffering();
        return $this->_renderedView;
    }

    public function getRenderedView()
    {
        return !empty($this->_renderedView) ? $this->_renderedView : null;
    }

    public function getViewName()
    {
        return $this->_viewName;
    }

    private function _loadHelperAndView()
    {
        $helperPath = Path::join(ROOT_PATH, 'application', 'view', $this->_viewName . '.helper.php');
        Files::loadIfExists($helperPath);

        $viewPath = Path::join(ROOT_PATH, 'application', 'view', $this->_viewName . '.phtml');
        $viewLoaded = $this->_requireIfExists($viewPath);
        if (!$viewLoaded) {
            throw new ViewException('No view found [' . $this->_viewName . ']');
        }
    }

    private function _renderUsingOutputBuffering()
    {
        ob_start();
        $this->_loadHelperAndView();
        $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }

    private function _loadHelpers()
    {
        $viewHelperPath = Path::join('Helper', 'ViewHelper.php');
        $appHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'ApplicationHelper.php');
        $formHelperPath = Path::join('Helper', 'FormHelper.php');
        $urlHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'UrlHelper.php');

        $this->_requireOnce($viewHelperPath);
        Files::loadIfExists($appHelperPath);
        $this->_requireOnce($formHelperPath);
        Files::loadIfExists($urlHelperPath);
    }

    private function _requireIfExists($path)
    {
        if (Files::exists($path)) {
            $this->_require($path);
            return true;
        }
        return false;
    }

    private function _requireOnce($path)
    {
        /** @noinspection PhpIncludeInspection */
        require_once($path);
    }

    private function _require($path)
    {
        /** @noinspection PhpIncludeInspection */
        require($path);
    }
}

class ViewException extends Exception
{
}