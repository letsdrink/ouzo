<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class UriHelperGenerator
{
    /** RouteRule[] */
    private $routes;

    /**
     * @return UriHelperGenerator
     */
    public static function generate()
    {
        $routes = Arrays::sort(Route::getRoutes(), Comparator::compareBy('getUri()'));
        return new self($routes);
        //return new self($routes);
    }

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function getGeneratedFunctions(): string
    {
        $namesAlreadyGenerated = [];
        $globalFunctions = [];
        $methods = [];
        foreach ($this->routes as $route) {
            if (!in_array($route->getName(), $namesAlreadyGenerated)) {
                list($methods[], $globalFunctions[]) = $this->createFunctionAndMethod($route);
            }
            $namesAlreadyGenerated[] = $route->getName();
        }

        $names = FluentArray::from($namesAlreadyGenerated)
            ->unique()
            ->filter(Functions::notEmpty())
            ->map(Functions::surroundWith("'"))
            ->toArray();
        $namesList = implode(",\n", $names);
        $methodsList = implode("\n", $methods);
        $globalFunctionsList = implode("\n", $globalFunctions);

        return UriGeneratorTemplates::replaceTemplate($namesList, $methodsList, $globalFunctionsList);
    }

    private function createFunctionAndMethod(RouteRule $routeRule): array
    {
        $name = $routeRule->getName();
        $uri = $routeRule->getUri();
        $parameters = $this->prepareParameterList($uri);
        $url = $this->getUrl($routeRule, $uri);
        $controller = '\\' . $routeRule->getController();
        $action = $routeRule->getAction();

        $method = UriGeneratorTemplates::method($controller, $action, $name, $parameters, $url);
        $function = UriGeneratorTemplates::function($controller, $action, $name, $parameters, $url);

        if ($name) {
            return [$method, $function];
        }
        return ['', ''];
    }

    private function prepareParameterList($uri): array
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        $parameters = Arrays::getValue($matches, 1, []);
        return Arrays::map($parameters, function ($parameter) {
            return '$' . $parameter;
        });
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
