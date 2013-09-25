<?php
use Ouzo\Routing\Route;
use Ouzo\Shell;
use Ouzo\Shell\InputArgument;

class RoutesShell extends Shell
{
    public function configure()
    {
        $this->addArgument('controller', 'c', InputArgument::VALUE_OPTIONAL);
    }

    public function main()
    {
        if ($this->getArgument('controller')) {
            $this->controller();
        } else {
            $this->all();
        }
    }

    private function controller()
    {
        $controller = $this->getArgument('controller');
        $this->_renderRoutes(Route::getRoutesForController($controller));
    }

    private function all()
    {
        $this->_renderRoutes(Route::getRoutes());
    }

    private function _renderRoutes($routes = array())
    {
        $prevMethod = '';
        foreach ($routes as $route) {
            $method = is_array($route->getMethod()) ? 'ANY' : $route->getMethod();
            if ($prevMethod != $method) {
                $showMethod = $method;
            } else {
                $showMethod = '';
            }

            $uri = $route->getUri();
            $action = $route->getAction() ? '#' . $route->getAction() : $route->getAction();
            $controllerAction = $route->getController() . $action;

            $text = sprintf("\t%-10s %-40s %s", $showMethod, $uri, $controllerAction);
            $this->out($text);
            $prevMethod = $method;
        }
    }
}