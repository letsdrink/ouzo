<?php
use Ouzo\Utilities\ClassName;

class ClassNameTest extends PHPUnit_Framework_TestCase
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