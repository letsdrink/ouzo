<?php
namespace Ouzo;

use Ouzo\Utilities\Strings;

class ControllerResolver
{
    function __construct($controllerPath = "\\Controller\\")
    {
        $globalConfig = Config::getValue('global');
        $this->_defaultAction = $globalConfig['action'];
        $this->controllerPath = $controllerPath;
        $this->_uri = new Uri();
    }

    public function  getController($controller, $action)
    {
        $controllerName = Strings::underscoreToCamelCase($controller);
        $controller = $this->controllerPath . $controllerName . "Controller";

        $this->_validateControllerExists($controller);

        return new $controller($action);
    }

    private function _validateControllerExists($controller)
    {
        if (!class_exists($controller)) {
            throw new FrontControllerException('Controller does not exist: ' . $controller);
        }
    }
}