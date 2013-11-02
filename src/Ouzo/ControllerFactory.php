<?php
namespace Ouzo;

use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Strings;

class ControllerFactory
{
    function __construct($controllerPath = "\\Controller\\")
    {
        $this->controllerPath = $controllerPath;
    }

    public function createController(RouteRule $routeRule)
    {
        $controller = $routeRule->getController();
        $controllerName = Strings::underscoreToCamelCase($controller);
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
class ControllerNotFoundException extends \Exception
{
}