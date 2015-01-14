<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests;

use Ouzo\Model;
use Ouzo\Tests\Mock\ArgumentMatcher;
use Ouzo\Utilities\Arrays;

class ModelAttributesMatcher implements ArgumentMatcher
{
    private $expectedAttributes;
    /**
     * @var Model
     */
    private $expected;

    public function __construct(Model $expected)
    {
        $this->expected = $expected;
        $this->expectedAttributes = $expectedAttributes = Arrays::filterByAllowedKeys($this->expected->attributes(), $this->expected->getFields());
    }

    public function matches($argument)
    {
        if (get_class($this->expected) !== get_class($argument)) {
            return false;
        }
        $actualAttributes = Arrays::filterByAllowedKeys($argument->attributes(), $this->expected->getFields());
        return $this->expectedAttributes == $actualAttributes;
    }

    public function __toString()
    {
        $attributes = print_r($this->expectedAttributes, true);
        return get_class($this->expected) . " with attributes($attributes)";
    }
}
