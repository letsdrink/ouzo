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

    public function __construct(string $uri, string $method, string $className, string $classMethod)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->className = $className;
        $this->classMethod = $classMethod;
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

    public function getClassMethod(): string
    {
        return $this->classMethod;
    }


}