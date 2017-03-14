<?php

namespace Ouzo\Routing;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class RouteCompiler
{
    /**
     * @param RouteRule[] $routes
     * @return array
     */
    public function generateTrie($routes)
    {
        $trie = array();
        foreach ($routes as $route) {
            $this->addRoute($trie, $route);
        }
        return $trie;
    }

    private function addRoute(&$trie, RouteRule $route)
    {
        $uri = $route->getUri();
        $parts = array_filter(explode('/', $uri));
        $parts = Arrays::map($parts, function ($part) {
            return Strings::startsWith($part, ':') ? ':id' : $part;
        });

        array_unshift($parts, '');

        if ($route->isActionRequired()) {
            array_push($parts, '/');
        } else {
            array_push($parts, '*');
        }

        foreach (Arrays::toArray($route->getMethod()) as $method) {
            $parts[0] = $method;
            $action = $route->getAction() ? $route->getController() . '#' . $route->getAction() : $route->getController();
            if (!Arrays::getNestedValue($trie, $parts)) {
                Arrays::setNestedValue($trie, $parts, array(
                    'action' => $action,
                    'uri' => $route->getUri()
                ));
            }
        }
    }
}