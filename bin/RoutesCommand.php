<?php
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Uri\UriHelperGenerator;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{
    /**
     * @var InputInterface
     */
    private $_input;
    /**
     * @var OutputInterface
     */
    private $_output;

    public function configure()
    {
        $this->setName('ouzo:routes')
            ->addOption('controller', 'c', InputOption::VALUE_OPTIONAL)
            ->addOption('generate', 'g', InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;

        if ($input->getOption('generate')) {
            $path = Path::join(ROOT_PATH, 'application', 'helper', 'GeneratedUriHelper.php');
            UriHelperGenerator::generate()->saveToFile($path);
            $output->writeln('File with uri helpers is generated in ' . $path);
        } else {
            if ($input->getOption('controller')) {
                $this->controller();
            } else {
                $this->all();
            }
        }
    }

    private function controller()
    {
        $controller = $this->_input->getOption('controller');
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

            $this->_output->writeln(sprintf("\t%30s \t %-10s %-40s %s", $name, $showMethod, $uri, $controllerAction));

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
            $obj->_output->writeln($text);
            Arrays::map($except, function ($except) use ($obj) {
                $obj->_output->writeln(sprintf("\t\t\t\t\t\t\t%-10s", $except));
                return $except;
            });
        }
    }
}