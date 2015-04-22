<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\View;

use Ouzo\ApplicationPaths;
use Ouzo\Config;
use Ouzo\Utilities\Path;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigRenderer implements ViewRenderer
{
    const EXTENSION = '.html.twig';

    private $_viewName;
    private $_attributes;
    private $_loaderPath;
    private $_viewPath;
    private $_viewFilename;

    function __construct($viewName, array $attributes)
    {
        $this->_viewName = $viewName;
        $this->_attributes = $attributes;
        $this->_loaderPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath());
        $this->_viewFilename = $viewName . self::EXTENSION;
        $this->_viewPath = Path::join($this->_loaderPath, $this->_viewFilename);
    }

    public function render()
    {
        $options = Config::getValue('twig', 'options') ?: array();
        $loader = new Twig_Loader_Filesystem($this->_loaderPath);
        $environment = new Twig_Environment($loader, $options);
        $environment->addExtension(new OuzoTwigExtension());
        $this->initialize($environment);
        $template = $environment->loadTemplate($this->_viewFilename);
        return $template->render($this->_attributes);
    }

    public function getViewPath()
    {
        return $this->_viewPath;
    }

    private function initialize(Twig_Environment $environment)
    {
        $initializerClass = Config::getValue('twig', 'initializer');
        if ($initializerClass) {
            $initializer = new $initializerClass();
            $initializer->initialize($environment);
        }
    }
}
