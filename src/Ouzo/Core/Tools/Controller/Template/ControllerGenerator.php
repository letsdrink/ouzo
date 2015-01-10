<?php
namespace Ouzo\Tools\Controller\Template;

use Ouzo\AutoloadNamespaces;
use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class ControllerGenerator
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function getClassName()
    {
        $class = Strings::underscoreToCamelCase($this->controller);
        if (Strings::endsWith($class, 'Controller')) {
            return $class;
        }
        return Strings::appendSuffix($class, 'Controller');
    }

    public function getClassNamespace()
    {
        return rtrim(AutoloadNamespaces::getControllerNamespace(), '\\');
    }

    public function isControllerExists()
    {
        return Files::exists($this->getControllerPath());
    }

    public function getControllerPath()
    {
        return ClassPathResolver::forClassAndNamespace($this->getClassName(), $this->getClassNamespace())->getClassFileName();
    }

    public function templateContents()
    {
        $classStubPlaceholderReplacer = new ControllerClassStubPlaceholderReplacer($this);
        return $classStubPlaceholderReplacer->content();
    }

    public function saveController()
    {
        $path = $this->getControllerPath();
        $this->preparePaths(dirname($path));
        file_put_contents($path, $this->templateContents());
    }

    private function preparePaths($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
