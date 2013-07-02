<?php
namespace Thulium;

use Thulium\Utilities\Strings;

class ControllerResolver
{
    function __construct()
    {
        $globalConfig = Config::load()->getConfig('global');
        $this->_defaultController = $globalConfig['controller'];
        $this->_uri = new Uri();
    }

    public function getCurrentController()
    {
        $controllerName = $this->_uri->getController();

        $controller = $controllerName ? "\\Controller\\" . $controllerName . "Controller" : "\\Controller\\" . Strings::underscoreToCamelCase($this->_defaultController) . "Controller";
        $controllerCustom = $controllerName ? "\\Controller\\" . $controllerName . "ControllerCustom" : null;

        $this->_validateControllerExists($controller, $controllerCustom);

        return class_exists($controllerCustom) ? new $controllerCustom() : new $controller();
    }

    private function _validateControllerExists($controller, $controllerCustom)
    {
        if (!class_exists($controllerCustom) && !class_exists($controller)) {
            throw new FrontControllerException('Controller does not exist: ' . $controller);
        }
    }
}