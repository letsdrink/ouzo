<?php
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
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
        foreach ($routes as $rule) {
            $method = $this->_getRuleMethod($rule);
            if ($prevMethod != $method) {
                $showMethod = $method;
            } else {
                $showMethod = '';
            }

            $uri = $rule->getUri();
            $action = $rule->getAction() ? '#' . $rule->getAction() : $rule->getAction();
            $controllerAction = $rule->getController() . $action;

            $text = sprintf("\t%-10s %-40s %s", $showMethod, $uri, $controllerAction);
            $this->out($text);
            $prevMethod = $method;
        }
    }

    private function _getRuleMethod(RouteRule $rule)
    {
        if (!$rule->isRequireAction()) {
            $method = 'ALL';
        } else {
            $method = is_array($rule->getMethod()) ? 'ANY' : $rule->getMethod();
        }
        return $method;
    }
}