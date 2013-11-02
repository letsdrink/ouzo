<?php
namespace Ouzo;

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
        $viewLoaded = Files::loadIfExists($viewPath, false);
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
        $viewHelperPath = Path::join(ROOT_PATH, 'src', 'Ouzo', 'Helper', 'ViewHelper.php');
        $appHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'ApplicationHelper.php');
        $urlHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'UrlHelper.php');
        $formHelperPath = Path::join(ROOT_PATH, 'src', 'Ouzo', 'Helper', 'FormHelper.php');

        Files::load($viewHelperPath);
        Files::loadIfExists($appHelperPath);
        Files::load($formHelperPath);
        Files::loadIfExists($urlHelperPath);
    }
}

class ViewException extends \Exception
{
}