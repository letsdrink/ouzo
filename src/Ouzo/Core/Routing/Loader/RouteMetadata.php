<?php

namespace Ouzo\Routing\Loader;

class RouteMetadata
{
    /** @var string */
    private $uri;

    /** @var string */
    private $method;

    /** @var string */
    private $className;

    /** @var string */
    private $classMethod;

    /** @var int */
    private $responseCode;

    public function __construct(string $uri, string $method, string $className, string $classMethod, ?int $responseCode)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->className = $className;
        $this->classMethod = $classMethod;
        $this->responseCode = $responseCode;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassNameReference(): string
    {
        return "\\{$this->className}::class";
    }
    
    public function getClassMethod(): string
    {
        return $this->classMethod;
    }

    public function hasParameters(): bool
    {
        return strpos($this->getUri(), ':') !== false;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }
}