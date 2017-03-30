<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class Router
{
    /**
     * @var Uri
     */
    private $uri;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return RouteRule
     * @throws RouterException
     */
    public function findRoute()
    {
        $path = $this->uri->getPathWithoutPrefix();
        $requestType = Uri::getRequestType();
        $rule = $this->findRouteRule($path, $requestType);
        if (!$rule) {
            throw new RouterException('No route rule found for HTTP method [' . $requestType . '] and URI [' . $path . ']');
        }
        $rule->setParameters($path);
        return $rule;
    }

    private function findRouteRule($path, $requestType)
    {
        $trie = RouteTrie::trie();
        if (!isset($trie[$requestType])) {
            return null;
        }

        $parts = array_values(array_filter(explode('/', $path)));

        array_push($parts, '/');

        $matched = $this->tryMatch($parts, 0, $trie[$requestType]);
        if (!$matched) {
            return null;
        }

        $explode = explode('#', $matched['action']);
        $controller = $explode[0];
        $action = null;
        if (count($explode) == 2) {
            $action = $explode[1];
        }
        return new RouteRule($requestType, $matched['uri'], $controller, $action, $action !== NULL);
    }

    private function tryMatch($parts, $partIndex, $trie)
    {
        for ($i = $partIndex; $i < count($parts); $i += 1 ) {
            $children = Arrays::getValue($trie, $parts[$i]);
            if (!$children) {
                if (isset($trie[':id'])) {
                    $tryMatch = $this->tryMatch($parts, $i + 1, $trie[':id']);
                    if ($tryMatch) {
                        return $tryMatch;
                    }
                }
                if (isset($trie['*'])) {
                    return $trie['*'];
                } else {
                    return null;
                }
            }
            $trie = $children;
        }
        return $trie;
    }
}
