<?php
namespace Ouzo\Tools\Controller\Template;

use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Strings;

class ViewGenerator
{
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function getViewName()
    {
        $class = Strings::underscoreToCamelCase($this->controller);
        if (Strings::endsWith($class, 'Controller')) {
            return Strings::removeSuffix($class, 'Controller');
        }
        return $class;
    }

    public function createViewDirectoryIfNotExists()
    {
        return $this->preparePaths($this->getViewPath());
    }

    public function getViewPath()
    {
        return ClassPathResolver::forClassAndNamespace($this->getViewName(), $this->getViewNamespace())->getClassDirectory();
    }

    public function getViewNamespace()
    {
        return '\\Application\\View';
    }

    private function preparePaths($path)
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }
        return false;
    }
}
