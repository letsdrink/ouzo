<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use ArrayAccess;
use BadMethodCallException;
use InvalidArgumentException;
use Ouzo\Utilities\Arrays;

/**
 * @property array get
 * @property array post
 * @property array route
 * @property array request
 */
class ControllerParameters implements ArrayAccess
{

    /**
     * @var array
     */
    private $routeParameters;
    /**
     * @var array
     */
    private $postParameters;
    /**
     * @var array
     */
    private $getParameters;
    /**
     * @var array
     */
    private $requestParameters;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $routeParameters = array(), array $postParameters = array(), array $getParameters = array(), array $requestParameters = array())
    {
        $this->routeParameters = $routeParameters;
        $this->postParameters = $postParameters;
        $this->getParameters = $getParameters;
        $this->requestParameters = $requestParameters;
        $this->parameters = array_merge($routeParameters, $postParameters, $getParameters, $requestParameters);
    }

    public function offsetExists($offset)
    {
        return isset($this->parameters[$offset]);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->parameters[$offset];
        }
        throw new InvalidArgumentException('Parameters does not contain specified key: ' . $offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->parameters[] = $value;
        } else {
            $this->parameters[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->parameters[$offset]);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'get':
                return $this->getParameters;
            case 'post':
                return $this->postParameters;
            case 'request':
                return $this->requestParameters;
            case 'route':
                return $this->routeParameters;
        }
        throw new InvalidArgumentException('Invalid field name: ' . $name);
    }

    public function getValue($key, $default = null)
    {
        return Arrays::getValue($this->parameters, $key, $default);
    }

    public function toArray()
    {
        return $this->parameters;
    }
}