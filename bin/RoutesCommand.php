<?php
namespace Command;

use Ouzo\ApplicationPaths;
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
            $path = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'GeneratedUriHelper.php');
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
        $table = $this->getHelper('table');
        $table->setHeaders(array('URL Helper', 'HTTP Verb', 'Path', 'Controller#Action'));

        foreach ($routes as $route) {
            $method = $this->_getRuleMethod($route);
            $action = $route->getAction() ? '#' . $route->getAction() : $route->getAction();
            $controllerAction = $route->getController() . $action;
            $table->addRow(array($route->getName(), $method, $route->getUri(), $controllerAction));
            $this->_printExceptIfExists($route, $table);
        }

        $table->render($this->_output);
    }

    private function _getRuleMethod(RouteRule $rule)
    {
        if (!$rule->isActionRequired()) {
            return 'ALL';
        }
        return is_array($rule->getMethod()) ? 'ANY' : $rule->getMethod();
    }

    private function _printExceptIfExists(RouteRule $rule, $table)
    {
        $except = $rule->getExcept();
        if ($except) {
            $table->addRow(array('', '', '  <info>except:</info>', ''));
            Arrays::map($except, function ($except) use ($table) {
                $table->addRow(array('', '', '    ' . $except, ''));
                return $except;
            });
        }
    }
}