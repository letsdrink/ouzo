<?php
namespace Ouzo;

use Exception;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\ClassName;

class ControllerFactory
{
    public function __construct()
    {
        $this->controllerPath = AutoloadPaths::getControllerPath();
    }

    public function createController(RouteRule $routeRule)
    {
        $controller = $routeRule->getController();
        $controllerName = ClassName::pathToFullyQualifiedName($controller);
        $controller = $this->controllerPath . $controllerName . "Controller";

        $this->_validateControllerExists($controller);

        return new $controller($routeRule);
    }

    private function _validateControllerExists($controller)
    {
        if (!class_exists($controller)) {
            throw new ControllerNotFoundException('Controller does not exist: ' . $controller);
        }
    }
}

class ControllerNotFoundException extends Exception
{
}
