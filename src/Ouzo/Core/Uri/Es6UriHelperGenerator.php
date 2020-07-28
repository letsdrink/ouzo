<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;

class Es6UriHelperGenerator
{
    const INDENT = '    ';

    private $generatedFunctions = '';

    /** @var RouteRule[] */
    public $routeRules;

    public function __construct()
    {
        $this->routeRules = $routes = Route::getRoutes();
        $this->generatedFunctions .= 'const checkParameter = (parameter) => {
' . self::INDENT . 'if (typeof parameter !== \'string\' && typeof parameter !== \'number\') {
' . self::INDENT . self::INDENT . 'throw new Error("Uri helper: Bad parameters");
' . self::INDENT . '}
};' . "\n\n";
        $this->generateFunctions();
    }

    private function generateFunctions()
    {
        $namesAlreadyGenerated = [];
        foreach ($this->routeRules as $routeRule) {
            $name = $routeRule->getName();
            if (!in_array($name, $namesAlreadyGenerated)) {
                $this->generatedFunctions .= $this->createFunction($routeRule);
            }
            $namesAlreadyGenerated[] = $name;
        }
    }

    private function createFunction(RouteRule $routeRule)
    {
        $uri = $routeRule->getUri();
        $applicationPrefix = UriGeneratorHelper::getApplicationPrefix($routeRule);
        $uriWithVariables = preg_replace('/:(\w+)/', '" + $1 + "', $uri);
        $name = $routeRule->getName();
        $parameters = $this->prepareParameters($uri);
        $parametersString = implode(', ', $parameters);
        $checkParametersStatement = $this->createCheckParameters($parameters);

        $function = <<<FUNCTION
export const $name = ($parametersString) => {
{$checkParametersStatement}return "$applicationPrefix$uriWithVariables";
};\n\n
FUNCTION;
        return $name ? $function : '';
    }

    private function prepareParameters($uri)
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        return Arrays::getValue($matches, 1, []);
    }

    private function createCheckParameters($parameters)
    {
        if ($parameters) {
            $indent = self::INDENT;
            $checkFunctionParameters = Arrays::map($parameters, function ($element) use ($indent) {
                return $indent . "checkParameter($element);";
            });
            return implode("\n", $checkFunctionParameters) . "\n" . $indent;
        }
        return self::INDENT;
    }

    public function getGeneratedFunctions()
    {
        return trim($this->generatedFunctions) . "\n";
    }

    public function saveToFile($path)
    {
        return file_put_contents($path, $this->getGeneratedFunctions());
    }

    public static function generate()
    {
        return new self();
    }
}
