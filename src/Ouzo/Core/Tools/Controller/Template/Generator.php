<?php
namespace Ouzo\Tools\Controller\Template;

use Ouzo\AutoloadNamespaces;
use Ouzo\Tools\Model\Template\ClassPathResolver;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Strings;

class Generator
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
        $classPath = ClassPathResolver::forClassAndNamespace($this->getClassName(), $this->getClassNamespace())->getClassFileName();
        return Files::exists($classPath);
    }
}
