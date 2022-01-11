<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Es6UriHelperGenerator
{
    /** @var string[] */
    private array $generatedParts = [];
    /** @var RouteRule[] */
    public array $routeRules;
    private Es6GeneratorTemplates $templates;

    public function __construct(private string $format)
    {
        $this->templates = new Es6GeneratorTemplates($format);
        $this->routeRules = $routes = Route::getRoutes();
        $this->generatedParts[] = $this->templates->checkParametersTemplate();
        $this->generateFunctions();
    }

    private function generateFunctions(): void
    {
        $functions = [];
        foreach ($this->routeRules as $routeRule) {
            $name = $routeRule->getName();
            if (!isset($functions[$name])) {
                $functions[$name] = $this->createFunction($routeRule);
            }
        }
        $this->generatedParts += $functions;
    }

    private function createFunction(RouteRule $routeRule): string
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

    /** @return string[] */
    private function prepareParameters(string $uri): array
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        return Arrays::getValue($matches, 1, []);
    }

    public function getGeneratedFunctions(): string
    {
        return trim(implode("\n\n", $this->generatedParts)) . "\n";
    }

    public function saveToFile(string $path): bool|int
    {
        return file_put_contents($path, $this->getGeneratedFunctions());
    }

    public static function generate(string $format = 'js'): self
    {
        return new self($format);
    }
}