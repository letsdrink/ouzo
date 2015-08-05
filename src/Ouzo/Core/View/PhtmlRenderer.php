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
    const EXTENSION = '.phtml';

    private $_viewName;
    private $_attributes;
    private $_viewPath;

    public function __construct($viewName, array $attributes)
    {
        $this->_viewName = $viewName;
        $this->_attributes = $attributes;
        $this->_viewPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $this->_viewName . self::EXTENSION);
    }

    public function render()
    {
        $this->_saveAttributes();
        ob_start();
        try {
            $this->_loadViewHelper();
            $this->_loadView();
        } catch (Exception $e) {
            ob_end_flush();
            throw $e;
        }
        $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }

    private function _loadViewHelper()
    {
        $helperPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $this->_viewName . '.helper.php');
        Files::loadIfExists($helperPath);
    }

    private function _loadView()
    {
        /** @noinspection PhpIncludeInspection */
        require($this->_viewPath);
    }

    private function _saveAttributes()
    {
        foreach ($this->_attributes as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getViewPath()
    {
        return $this->_viewPath;
    }
}
