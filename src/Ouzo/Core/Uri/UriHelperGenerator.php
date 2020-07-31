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
class GeneratedUriHelper {
    
%{INDENT}private static function checkParameter(\$parameter)
%{INDENT}{
%{INDENT}%{INDENT}if (!isset(\$parameter)) {
%{INDENT}%{INDENT}%{INDENT}throw new \\InvalidArgumentException(\"Missing parameters\");
%{INDENT}%{INDENT}}
%{INDENT}}\n\n";

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
        $globalFunctions = "";
        foreach ($this->_routes as $route) {
            if (!in_array($route->getName(), $namesAlreadyGenerated)) {
                list($method, $function) = $this->_createFunctionAndMethod($route);
                $this->_generatedFunctions .= $method;
                $globalFunctions .= $function;
            }
            $namesAlreadyGenerated[] = $route->getName();
        }

        $names = Joiner::on(",\n%{INDENT}%{INDENT}")->skipNulls()->mapValues(function ($value) {
            return "'$value'";
        })->join($namesAlreadyGenerated);

        $this->_generatedFunctions .= "%{INDENT}public static function allGeneratedUriNames()
%{INDENT}{
%{INDENT}%{INDENT}return array(" . $names . ");
%{INDENT}}
}\n\n";

        $this->_generatedFunctions .= $globalFunctions;
        $this->_generatedFunctions .= "function allGeneratedUriNames()
{
%{INDENT}return GeneratedUriHelper::allGeneratedUriNames();
}
\n\n";
    }

    private function _createFunctionAndMethod(RouteRule $routeRule)
    {
        $name = $routeRule->getName();
        $uri = $routeRule->getUri();
        $parameters = $this->_prepareParameters($uri);

        $url = $this->getUrl($routeRule, $uri);
        $parametersString = implode(', ', $parameters);

        $checkParametersStatement = $this->_createCheckParameters($parameters);

        $controller = '\\' . $routeRule->getController();
        $action = $routeRule->getAction();

        $method = <<<METHOD
%{INDENT}/**
%{INDENT} * @see {$controller}::{$action}()
%{INDENT} */
%{INDENT}public static function $name($parametersString)
%{INDENT}{
{$checkParametersStatement}%{INDENT}return "$url";
%{INDENT}}\n\n
METHOD;

        $function = <<<FUNCTION
/**
 * @see {$controller}::{$action}()
 */
function $name($parametersString)
{
%{INDENT}return GeneratedUriHelper::$name($parametersString);
}\n\n
FUNCTION;
        if ($name) {
            return [$method, $function];
        }
        return ['', ''];
    }

    private function _createCheckParameters($parameters)
    {
        if ($parameters) {
            $checkFunctionParameters = Arrays::map($parameters, function ($element) {
                return "%{INDENT}%{INDENT}GeneratedUriHelper::checkParameter($element);";
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
