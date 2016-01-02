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

    function __construct($className)
    {
        $this->className = $className;
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
}