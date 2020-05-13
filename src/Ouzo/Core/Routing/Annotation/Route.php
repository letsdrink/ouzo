<?php

namespace Ouzo\Routing\Annotation;

use BadMethodCallException;

abstract class Route
{
    private $path;
    private $methods = [];

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['path'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (!method_exists($this, $method)) {
                throw new BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, static::class));
            }
            $this->$method($value);
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods($methods)
    {
        $this->methods = $methods;
    }
}