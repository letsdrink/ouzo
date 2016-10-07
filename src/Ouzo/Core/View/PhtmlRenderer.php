<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\View;

use Exception;
use Ouzo\ApplicationPaths;
use Ouzo\Config;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class PhtmlRenderer implements ViewRenderer
{
    const EXTENSION = '.phtml';

    private $_viewName;
    private $_attributes;
    private $_viewPath;

    /** @var bool */
    private $_allowDebugTooltip;

    public function __construct($viewName, array $attributes, $allowDebugTooltip = true)
    {
        $this->_viewName = $viewName;
        $this->_attributes = $attributes;
        $this->_viewPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $this->_viewName . self::EXTENSION);
        $this->_allowDebugTooltip = $allowDebugTooltip;
    }

    public function render()
    {
        $this->_saveAttributes();
        ob_start();
        try {
            $this->_loadViewHelper();
            $this->_htmlDebugTooltipStart();
            $this->_loadView();
            $this->_htmlDebugTooltipEnd();
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

    private function _htmlDebugTooltipStart()
    {
        if ($this->shouldPrintDebugTooltip()) {
            echo '<!-- [PARTIAL] ' . $this->_viewName . ' -->';
        }
    }

    private function _htmlDebugTooltipEnd()
    {
        if ($this->shouldPrintDebugTooltip()) {
            echo '<!-- [END PARTIAL] ' . $this->_viewName . ' -->';
        }
    }

    private function shouldPrintDebugTooltip()
    {
        return $this->_allowDebugTooltip && Config::getValue('debug');
    }
}
