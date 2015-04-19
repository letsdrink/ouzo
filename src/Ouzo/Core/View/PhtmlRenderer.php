<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\View;

use Exception;
use Ouzo\ApplicationPaths;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class PhtmlRenderer implements ViewRenderer
{

    private $_viewName;
    private $_attributes;

    function __construct($viewName, array $attributes)
    {
        $this->_viewName = $viewName;
        $this->_attributes = $attributes;
    }

    public function render()
    {
        $this->_saveAttributes();
        ob_start();
        try {
            $this->_loadHelperAndView();
        } catch (Exception $e) {
            ob_end_flush();
            throw $e;
        }
        $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }

    private function _loadHelperAndView()
    {
        $helperPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $this->_viewName . '.helper.php');
        Files::loadIfExists($helperPath);

        $viewPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $this->_viewName . '.phtml');
        $viewLoaded = $this->_requireIfExists($viewPath);
        if (!$viewLoaded) {
            throw new ViewException('No view found [' . $this->_viewName . ']');
        }
    }

    private function _requireIfExists($path)
    {
        if (Files::exists($path)) {
            $this->_require($path);
            return true;
        }
        return false;
    }

    private function _require($path)
    {
        /** @noinspection PhpIncludeInspection */
        require($path);
    }

    private function _saveAttributes()
    {
        foreach ($this->_attributes as $name => $value) {
            $this->$name = $value;
        }
    }
}
