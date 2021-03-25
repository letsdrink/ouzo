<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class ModelAssert
{
    private ?Model $actual;

    private function __construct(?Model $actual)
    {
        $this->actual = $actual;
    }

    public static function that(?Model $actual): ModelAssert
    {
        return new ModelAssert($actual);
    }

    public function isEqualTo(Model $expected): void
    {
        $this->assertSameType($expected);
        AssertAdapter::assertEquals($expected->attributes(), $this->actual->attributes(), 'Models have different attributes ');
    }

    public function hasSameAttributesAs(Model $expected): void
    {
        $this->assertSameType($expected);
        $this->assertSamePersistentAttributes($expected);
    }

    private function assertSameType(Model $expected): void
    {
        AssertAdapter::assertEquals(get_class($expected), get_class($this->actual),
            'Expected object of type ' . $expected->getModelName() . ' but got ' . $this->actual->getModelName());
    }

    private function assertSamePersistentAttributes(Model $expected): void
    {
        $expectedAttributes = Arrays::filterByAllowedKeys($expected->attributes(), $expected->getFields());
        $actualAttributes = Arrays::filterByAllowedKeys($this->actual->attributes(), $this->actual->getFields());

        AssertAdapter::assertEquals($expectedAttributes, $actualAttributes, 'Models have different attributes ');
    }
}
