<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;

class UriHelperGenerator
{
    const INDENT = '    ';
    /**
     * RouteRule[]
     */
    private $_routes;
    private $_generatedFunctions = "<?php
function checkParameter(\$parameter)
{
%{INDENT}if (!isset(\$parameter)) {
%{INDENT}%{INDENT}throw new \\InvalidArgumentException(\"Missing parameters\");
%{INDENT}}
}\n\n";

    /**
     * @return UriHelperGenerator
     */
    public static function generate()
    {
        return new self(Route::getRoutes());
    }

    public function __construct($routes)
    {
        $this->_routes = $routes;
        $this->_generateFunctions();
    }

    private function _generateFunctions()
    {
        $namesAlreadyGenerated = [];
        foreach ($this->_routes as $route) {
            if (!in_array($route->getName(), $namesAlreadyGenerated)) {
                $this->_generatedFunctions .= $this->_createFunction($route);
            }
            $namesAlreadyGenerated[] = $route->getName();
        }

        $names = Joiner::on(",\n%{INDENT}%{INDENT}")->skipNulls()->mapValues(function ($value) {
            return "'$value'";
        })->join($namesAlreadyGenerated);

        $this->_generatedFunctions .= "function allGeneratedUriNames()
{
%{INDENT}return array(" . $names . ");
}\n\n";
    }

    private function _createFunction(RouteRule $routeRule)
    {
        $name = $routeRule->getName();
        $uri = $routeRule->getUri();
        $parameters = $this->_prepareParameters($uri);

        $url = $this->getUrl($routeRule, $uri);
        $parametersString = implode(', ', $parameters);

        $checkParametersStatement = $this->_createCheckParameters($parameters);

        $controller = '\\' . $routeRule->getController();
        $action = $routeRule->getAction();

        $function = <<<FUNCTION
/**
 * @see {$controller}::{$action}()
 */
function $name($parametersString)
{
{$checkParametersStatement}return "$url";
}\n\n
FUNCTION;
        return $name ? $function : '';
    }

    private function _createCheckParameters($parameters)
    {
        if ($parameters) {
            $checkFunctionParameters = Arrays::map($parameters, function ($element) {
                return "%{INDENT}checkParameter($element);";
            });
            return implode("\n", $checkFunctionParameters) . "\n%{INDENT}";
        }
        return "%{INDENT}";
    }

    private function _prepareParameters($uri)
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        $parameters = Arrays::getValue($matches, 1, []);
        return Arrays::map($parameters, function ($parameter) {
            return '$' . $parameter;
        });
    }

    public function getGeneratedFunctions()
    {
        return trim(Strings::sprintAssoc($this->_generatedFunctions, ['INDENT' => self::INDENT]));
    }

    public function saveToFile($file)
    {
        return file_put_contents($file, $this->getGeneratedFunctions());
    }

    private function getUrl(RouteRule $routeRule, $uri)
    {
        $uriWithVariables = str_replace(':', '$', $uri);
        $prefix = UriGeneratorHelper::getApplicationPrefix($routeRule);
        return $prefix . $uriWithVariables;
    }
}
