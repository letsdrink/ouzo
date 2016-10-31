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
use Ouzo\Utilities\Strings;

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
            $this->_debugTooltipStart();
            $this->_loadView();
            $this->_dDebugTooltipEnd();
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

    private function _debugTooltipStart()
    {
        if ($this->_isViewJavaScriptFile()) {
            $this->_jsDebugTooltipStart();
        }
        else {
            $this->_htmlDebugTooltipStart();
        }
    }

    private function _dDebugTooltipEnd()
    {
        if ($this->_isViewJavaScriptFile()) {
            $this->_jsDebugTooltipEnd();
        }
        else {
            $this->_htmlDebugTooltipEnd();
        }
    }

    private function _isViewJavaScriptFile()
    {
        return Strings::endsWith($this->_viewName, '_js.phtml') || Strings::endsWith($this->_viewName, '.js.phtml');
    }

    private function _jsDebugTooltipStart()
    {
        if ($this->shouldPrintDebugTooltip()) {
            echo '/** [PARTIAL] ' . $this->_viewName . ' */';
        }
    }

    private function _jsDebugTooltipEnd()
    {
        if ($this->shouldPrintDebugTooltip()) {
            echo '/** [END PARTIAL] ' . $this->_viewName . ' */';
        }
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
        return Config::getValue('debug');
    }
}
