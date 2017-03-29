<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Command;

use Ouzo\ApplicationPaths;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteCompiler;
use Ouzo\Routing\RouteRule;
use Ouzo\Uri\JsUriHelperGenerator;
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
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path for JS helper generated file', Path::join(ROOT_PATH, 'public'))
            ->addOption('generate-all', 'a', InputOption::VALUE_NONE)
            ->addOption('generate-php', 'g', InputOption::VALUE_NONE)
            ->addOption('generate-js', 'j', InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;
        $generateOptionFunctionMap = [
            "generate-php" => "generatePhpHelper",
            "generate-js" => "generateJsHelper",
            "generate-all" => "generateAllHelpers",
        ];
        $selectedOptions = array_filter($input->getOptions());
        $selectedGeneratedOptions = array_intersect(array_keys($selectedOptions), array_keys($generateOptionFunctionMap));

        if (sizeof($selectedGeneratedOptions)) {
            $this->runSelectedGenerators($selectedGeneratedOptions, $generateOptionFunctionMap);
        } else {
            if ($input->getOption('controller')) {
                $this->controller();
            } else {
                $this->all();
            }
        }
    }

    private function runSelectedGenerators($selectedGeneratedOptions, $generateOptionFunctionMap)
    {
        foreach ($generateOptionFunctionMap as $optionName => $functionName) {
            if (in_array($optionName, $selectedGeneratedOptions)) {
                call_user_func([$this, $functionName]);
            }
        }
    }

    public function generateAllHelpers()
    {
        $this->generatePhpHelper();
        $this->generateJsHelper();
        $this->compileRoutes();
    }

    private function generatePhpHelper()
    {
        $routesPhpHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'GeneratedUriHelper.php');
        if (UriHelperGenerator::generate()->saveToFile($routesPhpHelperPath) !== false) {
            $this->_output->writeln("File with PHP uri helpers is generated in <info>$routesPhpHelperPath</info>");
        }
    }

    private function compileRoutes()
    {
        $routesPhpHelperPath = Path::join(ROOT_PATH, ApplicationPaths::getHelperPath(), 'CompiledRoutes.php');
        $routeCompiler = new RouteCompiler();
        $trie = $routeCompiler->generateTrie(Route::getRoutes());
        $code = '<?php namespace Helper; class CompiledRoutes { static function trie() { static $routes =  ' . var_export($trie, true) . '; return $routes;}}';

        $code = preg_replace('/(\n|\s)+/m', ' ', $code);
        $code = preg_replace('/,\s*\)/m', ')', $code);

        file_put_contents($routesPhpHelperPath, $code);
        $this->_output->writeln("File with PHP routes is generated in <info>$routesPhpHelperPath</info>");
    }

    private function generateJsHelper()
    {
        $routesJSHelperPath = $this->_input->getOption('path');
        $routesJSHelperPath = Path::join($routesJSHelperPath, 'generated_uri_helper.js');
        if (JsUriHelperGenerator::generate()->saveToFile($routesJSHelperPath) !== false) {
            $this->_output->writeln("File with JS uri helpers is generated in <info>$routesJSHelperPath</info>");
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

    private function _renderRoutes($routes = [])
    {
        $table = $this->getHelper('table');
        $table->setHeaders(['URL Helper', 'HTTP Verb', 'Path', 'Controller#Action']);

        foreach ($routes as $route) {
            $method = $this->_getRuleMethod($route);
            $action = $route->getAction() ? '#' . $route->getAction() : $route->getAction();
            $controllerAction = $route->getController() . $action;
            $table->addRow([$route->getName(), $method, $route->getUri(), $controllerAction]);
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
            $table->addRow(['', '', '  <info>except:</info>', '']);
            Arrays::map($except, function ($except) use ($table) {
                $table->addRow(['', '', '    ' . $except, '']);
                return $except;
            });
        }
    }
}
