<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

use Ouzo\Http\HttpMethod;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;

class RouteRule
{
    private array $parameters = [];

    public function __construct(
        private array|string $method,
        private string $uri,
        private string $controller,
        private ?string $action,
        private bool $requireAction,
        private array $options = [],
        private bool $isResource = false
    )
    {
    }

    public function getMethod(): array|string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function isRequiredAction(): bool
    {
        return $this->requireAction;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function hasRequiredAction(): bool
    {
        if ($this->requireAction) {
            return (in_array($this->method, [HttpMethod::GET, HttpMethod::POST]) || is_array($this->method)) && !$this->action;
        }

        return $this->requireAction;
    }

    public function matches(string $uri, string $requestType): bool
    {
        if ($this->isEqualOrAnyMethod($requestType)) {
            return $this->match($uri);
        }
        return false;
    }

    private function isEqualOrAnyMethod(string $requestType): bool
    {
        return is_array($this->method) ? in_array($requestType, $this->method) : $requestType === $this->method;
    }

    private function match(string $uri): bool
    {
        preg_match('#/.+?/(.+?)(/|$)#', $uri, $matches);
        if ($this->isInExceptActions(Arrays::getValue($matches, 1, ''))) {
            return false;
        }
        $definedUri = $this->getUri();
        if ($definedUri === $uri) {
            return true;
        }
        if (strstr($definedUri, ':') !== false) {
            $replacedUri = preg_replace('#:\w*#u', '[\w.\-~_%@ \+]+', $definedUri);
            return preg_match('#^' . $replacedUri . '$#u', $uri) === 1;
        }
        if (!$this->action) {
            return preg_match('#^' . $definedUri . '/#u', $uri) === 1;
        }
        return false;
    }

    public function isInExceptActions(string $action): bool
    {
        return in_array($action, $this->getExcept());
    }

    public function getExcept()
    {
        return Arrays::getValue($this->options, 'except', []);
    }

    public function setParameters($uri)
    {
        $ruleUri = explode('/', $this->getUri());
        $requestUri = explode('/', $uri);

        $filterParameters = FluentArray::from($ruleUri)
            ->filter(function ($parameter) {
                return preg_match('#:\w+#', $parameter);
            })
            ->map(function ($parameter) {
                return str_replace(':', '', $parameter);
            })
            ->toArray();

        $filterValues = array_intersect_key($requestUri, $filterParameters);

        $this->parameters = Arrays::combine($filterParameters, $filterValues);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getControllerName(): string
    {
        $name = implode('', $this->getControllerParts());
        return Strings::underscoreToCamelCase($name);
    }

    public function getName(): string
    {
        $name = Arrays::getValue($this->options, 'as', $this->prepareRuleName());
        $nameWithPath = Strings::appendSuffix($name, '_path');
        $name = lcfirst(Strings::underscoreToCamelCase($nameWithPath));
        return $this->requireAction ? $name : '';
    }

    private function prepareRuleName(): string
    {
        return $this->isResource ? $this->getNameToRest() : $this->getNameToNonRest();
    }

    private function getNameToRest(): string
    {
        return $this->prepareResourceActionName() . $this->prepareResourceControllerName();
    }

    private function prepareResourceActionName(): string
    {
        $action = $this->action;
        if (in_array($action, ['fresh', 'edit'])) {
            return "{$action}_";
        }
        return '';
    }

    private function prepareResourceControllerName(): string
    {
        $controllerParts = $this->getControllerParts();
        $result = [];

        foreach ($controllerParts as $controllerPart) {
            $parts = explode('_', $controllerPart);
            if (in_array($this->action, ['index', 'create'])) {
                $suffix = array_pop($parts);
            } else {
                $suffix = Inflector::singularize(array_pop($parts));
            }
            array_push($parts, $suffix);
            $result[] = implode('_', $parts);
        }

        rsort($result);
        return implode('_', $result);
    }

    private function getNameToNonRest(): string
    {
        return "{$this->action}_{$this->handleNestedResource()}";
    }

    private function handleNestedResource(): string
    {
        $parts = $this->getControllerParts();
        rsort($parts);
        return implode('_', $parts);
    }

    private function getControllerParts(): array
    {
        $parts = explode('\\', $this->controller);
        $parts = Arrays::map($parts, function ($part) {
            $part = Strings::removeSuffix($part, "Controller");
            return Strings::removeSuffix($part, "Application");
        });
        $controllerName = Strings::camelCaseToUnderscore(array_pop($parts));
        $parts = Arrays::map($parts, 'strtolower');
        $parts[] = $controllerName;
        return array_filter($parts);
    }
}
