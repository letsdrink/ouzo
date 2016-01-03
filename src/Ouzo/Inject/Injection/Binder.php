<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;


class Binder
{
    private $className;
    private $boundClassName;
    private $scope = Scope::PROTOTYPE;
    private $instance;
    private $name;

    function __construct($className, $name = '')
    {
        $this->className = $className;
        $this->name = $name;
    }

    public function in($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function to($boundClassName)
    {
        $this->boundClassName = $boundClassName;
        return $this;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getBoundClassName()
    {
        return $this->boundClassName;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function toInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getName()
    {
        return $this->name;
    }
}