<?php
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Shell;
use Ouzo\Shell\InputArgument;
use Ouzo\Uri\UriHelperGenerator;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class RoutesShell extends Shell
{
    public function configure()
    {
        $this->addArgument('controller', 'c', InputArgument::VALUE_OPTIONAL);
        $this->addArgument('generate', 'g', InputArgument::VALUE_NONE);
    }

    public function main()
    {
        if ($this->getArgument('generate')) {
            $path = Path::join(ROOT_PATH, 'application', 'helper', 'GeneratedUriHelper.php');
            UriHelperGenerator::generate()->saveToFile($path);
            $this->out('File with uri helpers is generated in ' . $path);
        } else {
            if ($this->getArgument('controller')) {
                $this->controller();
            } else {
                $this->all();
            }
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

            $name = $rule->getName();
            $uri = $rule->getUri();
            $action = $rule->getAction() ? '#' . $rule->getAction() : $rule->getAction();
            $controllerAction = $rule->getController() . $action;

            $text = sprintf("\t%30s \t %-10s %-40s %s", $name, $showMethod, $uri, $controllerAction);
            $this->out($text);

            $this->_printExceptIfExists($rule);

            $prevMethod = $method;
        }
    }

    private function _getRuleMethod(RouteRule $rule)
    {
        if (!$rule->isActionRequired()) {
            return 'ALL';
        }
        return is_array($rule->getMethod()) ? 'ANY' : $rule->getMethod();
    }

    private function _printExceptIfExists(RouteRule $rule)
    {
        $except = $rule->getExcept();
        if ($except) {
            $obj = $this;
            $text = sprintf("\t\t\t\t\t\t%13s", 'except:');
            $obj->out($text);
            Arrays::map($except, function ($except) use ($obj) {
                $text = sprintf("\t\t\t\t\t\t\t%-10s", $except);
                $obj->out($text);
                return $except;
            });
        }
    }
}