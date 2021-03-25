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

    private string $loaderPath;
    private string $viewPath;
    private string $viewFilename;

    public function __construct(private string $viewName, private array $attributes)
    {
        $this->loaderPath = Path::join(ROOT_PATH, ApplicationPaths::getViewPath());
        $this->viewFilename = $viewName . self::EXTENSION;
        $this->viewPath = Path::join($this->loaderPath, $this->viewFilename);
    }

    public function render(): string
    {
        $options = Config::getValue('twig', 'options') ?: [];
        $loader = new Twig_Loader_Filesystem($this->loaderPath);
        $environment = new Twig_Environment($loader, $options);
        $environment->addExtension(new OuzoTwigExtension());
        $this->initialize($environment);
        $template = $environment->loadTemplate($this->viewFilename);
        return $template->render($this->attributes);
    }

    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    private function initialize(Twig_Environment $environment): void
    {
        $initializerClass = Config::getValue('twig', 'initializer');
        if ($initializerClass) {
            $initializer = new $initializerClass();
            $initializer->initialize($environment);
        }
    }
}
