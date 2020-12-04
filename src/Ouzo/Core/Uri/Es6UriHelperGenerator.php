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
    private string $format;
    private Es6GeneratorTemplates $templates;

    public function __construct(string $format)
    {
        $this->templates = new Es6GeneratorTemplates($format);
        $this->routeRules = $routes = Route::getRoutes();
        $this->generatedParts[] = $this->templates->checkParametersTemplate();
        $this->generateFunctions();
        $this->format = $format;
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
            $function = $this->templates->getFunction($name, $parameters, "$applicationPrefix$uriWithVariables");
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

    public static function generate(string $format = 'js')
    {
        return new self($format);
    }
}