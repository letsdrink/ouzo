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
            throw new ViewException('Type view name');
        }

        $this->_viewName = $viewName;

        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        $viewHelperPath = Path::join(ROOT_PATH, 'src', 'Ouzo', 'Helper', 'ViewHelper.php');
        $appHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'ApplicationHelper.php');
        $urlHelperPath = Path::join(ROOT_PATH, 'application', 'helper', 'UrlHelper.php');
        $formHelperPath = Path::join(ROOT_PATH, 'src', 'Ouzo', 'Helper', 'FormHelper.php');

        Files::load($viewHelperPath);
        Files::loadIfExists($appHelperPath);
        Files::load($formHelperPath);
        Files::loadIfExists($urlHelperPath);
    }

    public function render($viewName = '')
    {
        if (!empty($viewName)) {
            $this->_viewName = $viewName;
        }

        ob_start();

        $helperPath = Path::join(ROOT_PATH, 'application', 'view', $this->_viewName . '.helper.php');
        $viewPath = Path::join(ROOT_PATH, 'application', 'view', $this->_viewName . '.phtml');

        Files::loadIfExists($helperPath);
        $viewLoaded = Files::loadIfExists($viewPath, false);
        if (!$viewLoaded) {
            throw new ViewException('No view found [' . $this->_viewName . ']');
        }

        $view = ob_get_contents();
        ob_end_clean();

        $this->_renderedView = $view;

        return $view;
    }

    public function getRenderedView()
    {
        return !empty($this->_renderedView) ? $this->_renderedView : null;
    }

    public function getViewName()
    {
        return $this->_viewName;
    }
}

class ViewException extends \Exception
{
}