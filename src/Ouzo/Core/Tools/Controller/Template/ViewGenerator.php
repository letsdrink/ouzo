<?php
namespace Ouzo\Tools\Controller\Template;

use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use RuntimeException;

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

    public function appendAction(ActionGenerator $actionGenerator = null)
    {
        if ($actionGenerator) {
            if ($this->isActionExists($actionGenerator->getActionViewFile())) {
                throw new RuntimeException('Action file is already defined');
            }
            $actionAppender = new ActionAppender($actionGenerator);
            return $actionAppender->toView($this)->append();
        }
        return false;
    }

    public function isActionExists($actionFile)
    {
        return Files::exists(Path::join($this->getViewPath(), $actionFile));
    }
}
