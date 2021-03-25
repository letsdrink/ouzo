<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Comparator;

class JsUriHelperGenerator
{
    const INDENT = '    ';

    private string $generatedFunctions = '';
    /** @var RouteRule[] */
    public array $routeRules;

    public function __construct()
    {
        $this->routeRules = $routes = Arrays::sort(Route::getRoutes(), Comparator::compareBy('getUri()'));
        $this->generatedFunctions .= 'function checkParameter(parameter) {
' . self::INDENT . 'if (parameter === null) {
' . self::INDENT . self::INDENT . 'throw new Error("Uri helper: Missing parameters");
' . self::INDENT . '}
}' . "\n\n";
        $this->generateFunctions();
    }

    private function generateFunctions(): void
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

    private function createFunction(RouteRule $routeRule): string
    {
        $uri = $routeRule->getUri();
        $applicationPrefix = UriGeneratorHelper::getApplicationPrefix($routeRule);
        $uriWithVariables = preg_replace('/:(\w+)/', '" + $1 + "', $uri);
        $name = $routeRule->getName();
        $parameters = $this->prepareParameters($uri);
        $parametersString = implode(', ', $parameters);
        $checkParametersStatement = $this->createCheckParameters($parameters);

        $function = <<<FUNCTION
function $name($parametersString) {
{$checkParametersStatement}return "$applicationPrefix$uriWithVariables";
}\n\n
FUNCTION;
        return $name ? $function : '';
    }

    /** @return string[] */
    private function prepareParameters(string $uri): array
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        return Arrays::getValue($matches, 1, []);
    }

    private function createCheckParameters(array $parameters): string
    {
        if ($parameters) {
            $checkFunctionParameters = Arrays::map($parameters, fn($element) => self::INDENT . "checkParameter($element);");
            return implode("\n", $checkFunctionParameters) . "\n" . self::INDENT;
        }
        return self::INDENT;
    }

    public function getGeneratedFunctions(): string
    {
        return trim($this->generatedFunctions) . "\n";
    }

    public function saveToFile(string $path): bool|int
    {
        return file_put_contents($path, $this->getGeneratedFunctions());
    }

    public static function generate(): self
    {
        return new self();
    }
}
