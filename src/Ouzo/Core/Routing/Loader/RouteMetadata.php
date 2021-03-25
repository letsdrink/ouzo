<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

class RouteMetadata
{
    private string $uri;
    private string $httpMethod;
    private string $className;
    private string $classMethod;
    private ?int $responseCode;

    public function __construct(string $uri, string $httpMethod, string $className, string $classMethod, ?int $responseCode)
    {
        $this->uri = $uri;
        $this->httpMethod = $httpMethod;
        $this->className = $className;
        $this->classMethod = $classMethod;
        $this->responseCode = $responseCode;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassMethod(): string
    {
        return $this->classMethod;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function getClassNameReference(): string
    {
        return "\\{$this->className}::class";
    }

    public function hasParameters(): bool
    {
        return str_contains($this->getUri(), ':');
    }
}
