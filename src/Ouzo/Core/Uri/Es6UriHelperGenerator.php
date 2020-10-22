<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Es6UriHelperGenerator
{
    private array $generatedParts = [];

    /** @var RouteRule[] */
    public $routeRules;

    public function __construct()
    {
        $this->routeRules = $routes = Route::getRoutes();
        $this->generatedParts[] = Es6GeneratorTemplates::checkParametersTemplate();
        $this->generateFunctions();
    }

    private function generateFunctions()
    {
        $functions = [];
        foreach ($this->routeRules as $routeRule) {
            $name = $routeRule->getName();
            $functions[$name] = $this->createFunction($routeRule);
        }
        $this->generatedParts += $functions;
    }

    private function createFunction(RouteRule $routeRule)
    {
        $uri = $routeRule->getUri();
        $applicationPrefix = UriGeneratorHelper::getApplicationPrefix($routeRule);
        $uriWithVariables = preg_replace('/:(\w+)/', "' + $1 + '", $uri);
        $name = $routeRule->getName();
        $parameters = $this->prepareParameters($uri);
        if ($name) {
            $function = Es6GeneratorTemplates::getFunction($name, $parameters, "$applicationPrefix$uriWithVariables");
            return Strings::remove($function, " + ''");
        }
        return '';
    }

    private function prepareParameters($uri)
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        return Arrays::getValue($matches, 1, []);
    }

    public function getGeneratedFunctions()
    {
        return trim(implode("\n\n", $this->generatedParts)) . "\n";
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