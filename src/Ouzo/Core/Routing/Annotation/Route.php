<?php

namespace Ouzo\Routing\Annotation;

use BadMethodCallException;

abstract class Route
{
    /** @var string */
    private $path;

    /** @var string[] */
    private $methods = [];

    /**
     * @param array $data
     */
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

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string[] $methods
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }
}