<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Some\Test\Ns;

use Ouzo\Utilities\ToString\ToStringBuilder;
use Ouzo\Utilities\ToString\ToStringStyle;
use PHPUnit\Framework\TestCase;

class ToStringBuilderClass
{
    private string $name;
    private int $age;
    private bool $smoking;
    private array $tags;
    private array $customFields;
    private ?string $nullable;
    private object $classWithoutToString;
    private object $classWithToString;

    private ?ToStringStyle $style = null;

    public function __construct($name, $age, $smoking, $tags, $customFields, $nullable, $classWithoutToString, $classWithToString)
    {
        $this->name = $name;
        $this->age = $age;
        $this->smoking = $smoking;
        $this->tags = $tags;
        $this->customFields = $customFields;
        $this->nullable = $nullable;
        $this->classWithoutToString = $classWithoutToString;
        $this->classWithToString = $classWithToString;
    }

    public function setStyle(ToStringStyle $style): void
    {
        $this->style = $style;
    }

    public function __toString(): string
    {
        return (new ToStringBuilder($this, $this->style))
            ->append('name', $this->name)
            ->append('age', $this->age)
            ->append('smoking', $this->smoking)
            ->append('tags', $this->tags)
            ->append('customFields', $this->customFields)
            ->append('nullable', $this->nullable)
            ->append('classWithoutToString', $this->classWithoutToString)
            ->append('classWithToString', $this->classWithToString)
            ->toString();
    }
}

class ClassWithoutToString
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }
}

class ClassWithToString
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return (new ToStringBuilder($this))
            ->append('string', $this->string)
            ->toString();
    }
}

class ToStringBuilderTest extends TestCase
{
    private ToStringBuilderClass $toStringBuilderClass;

    public function setUp(): void
    {
        parent::setUp();

        $string = "jon";
        $int = 91;
        $boolean = true;
        $array = ['tag1', 'tag2', 'another tag'];
        $map = ['field1' => 'value1', 'field2' => 'value2'];
        $nullable = null;
        $classWithoutToString = new ClassWithoutToString("some string");
        $classWithToString = new ClassWithToString("some new string");

        $this->toStringBuilderClass = new ToStringBuilderClass($string, $int, $boolean, $array, $map, $nullable, $classWithoutToString, $classWithToString);
    }

    /**
     * @test
     */
    public function shouldUseDefaultStyle()
    {
        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = 'Some\Test\Ns\ToStringBuilderClass[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag},customFields={field1=value1,field2=value2},nullable=<null>,classWithoutToString=Some\Test\Ns\ClassWithoutToString,classWithToString=Some\Test\Ns\ClassWithToString[string=some new string]]';
        $this->assertEquals($expected, $toString);
    }

    /**
     * @test
     */
    public function shouldUseNoFieldNameStyle()
    {
        //given
        $this->toStringBuilderClass->setStyle(ToStringStyle::noFieldNamesStyle());

        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = 'Some\Test\Ns\ToStringBuilderClass[jon,91,true,{tag1,tag2,another tag},{field1=value1,field2=value2},<null>,Some\Test\Ns\ClassWithoutToString,Some\Test\Ns\ClassWithToString[string=some new string]]';
        $this->assertEquals($expected, $toString);
    }

    /**
     * @test
     */
    public function shouldUseShortPrefixStyle()
    {
        //given
        $this->toStringBuilderClass->setStyle(ToStringStyle::shortPrefixStyle());

        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = 'ToStringBuilderClass[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag},customFields={field1=value1,field2=value2},nullable=<null>,classWithoutToString=Some\Test\Ns\ClassWithoutToString,classWithToString=Some\Test\Ns\ClassWithToString[string=some new string]]';
        $this->assertEquals($expected, $toString);
    }

    /**
     * @test
     */
    public function shouldUseSimpleStyle()
    {
        //given
        $this->toStringBuilderClass->setStyle(ToStringStyle::simpleStyle());

        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = 'jon,91,true,{tag1,tag2,another tag},{field1=value1,field2=value2},<null>,Some\Test\Ns\ClassWithoutToString,Some\Test\Ns\ClassWithToString[string=some new string]';
        $this->assertEquals($expected, $toString);
    }

    /**
     * @test
     */
    public function shouldUseNoClassNameStyle()
    {
        //given
        $this->toStringBuilderClass->setStyle(ToStringStyle::noClassNameStyle());

        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = '[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag},customFields={field1=value1,field2=value2},nullable=<null>,classWithoutToString=Some\Test\Ns\ClassWithoutToString,classWithToString=Some\Test\Ns\ClassWithToString[string=some new string]]';
        $this->assertEquals($expected, $toString);
    }

    /**
     * @test
     */
    public function shouldUseMultiLineStyle()
    {
        //given
        $this->toStringBuilderClass->setStyle(ToStringStyle::multiLineStyle());

        //when
        $toString = $this->toStringBuilderClass->__toString();

        //then
        $expected = 'Some\Test\Ns\ToStringBuilderClass[' . PHP_EOL .
            '  name=jon' . PHP_EOL .
            '  age=91' . PHP_EOL .
            '  smoking=true' . PHP_EOL .
            '  tags={tag1,tag2,another tag}' . PHP_EOL .
            '  customFields={field1=value1,field2=value2}' . PHP_EOL .
            '  nullable=<null>' . PHP_EOL .
            '  classWithoutToString=Some\Test\Ns\ClassWithoutToString' . PHP_EOL .
            '  classWithToString=Some\Test\Ns\ClassWithToString[string=some new string]' . PHP_EOL .
            ']';
        $this->assertEquals($expected, $toString);
    }
}
