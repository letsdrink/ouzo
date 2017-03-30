<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

class Binder
{
    /** @var string */
    private $className;
    /** @var string */
    private $boundClassName;
    /** @var string */
    private $scope = Scope::PROTOTYPE;
    /** @var object */
    private $instance;
    /** @var string */
    private $name;

    /**
     * @param string $className
     * @param string $name
     */
    public function __construct($className, $name = '')
    {
        $this->className = $className;
        $this->name = $name;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function in($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @param string $boundClassName
     * @return $this
     */
    public function to($boundClassName)
    {
        $this->boundClassName = $boundClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getBoundClassName()
    {
        return $this->boundClassName;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param object $instance
     * @return $this
     */
    public function toInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @return object
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
