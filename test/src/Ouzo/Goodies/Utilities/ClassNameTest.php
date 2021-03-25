<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\ClassName;

use PHPUnit\Framework\TestCase; 

class ClassNameTest extends TestCase
{
    /**
     * @test
     */
    public function shouldTransformStringToNamespace()
    {
        //given
        $string = 'api/multiple_ns';

        //when
        $namespace = ClassName::pathToFullyQualifiedName($string);

        //then
        $this->assertEquals('Api\\MultipleNs', $namespace);
    }

    /**
     * @test
     */
    public function shouldNotTransformWhenStringNotHaveNamespace()
    {
        //given
        $string = 'some_string';

        //when
        $namespace = ClassName::pathToFullyQualifiedName($string);

        //then
        $this->assertEquals('SomeString', $namespace);
    }
}
