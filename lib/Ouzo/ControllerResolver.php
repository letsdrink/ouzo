<?php
namespace Ouzo;

class ControllerResolver
{
    function __construct($controllerPath = "\\Controller\\")
    {
        $globalConfig = Config::getValue('global');
        $this->_defaultAction = $globalConfig['action'];
        $this->controllerPath = $controllerPath;
        $this->_uri = new Uri();
    }

    public function getCurrentController()
    {
        $controllerName = $this->_uri->getController();
        $controller = $this->controllerPath . $controllerName . "Controller";

        $this->_validateControllerExists($controller);

        return new $controller($this->_getCurrentAction());
    }

    private function _getCurrentAction()
    {
        return $this->_uri->getAction() ? : $this->_defaultAction;
    }

    private function _validateControllerExists($controller)
    {
        if (!class_exists($controller)) {
            throw new FrontControllerException('Controller does not exist: ' . $controller);
        }
    }
}