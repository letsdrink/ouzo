<?php

namespace Ouzo\Tests;

use PHPUnit_Framework_Assert;

/**
 * Fluent string assertions inspired by java fest assertions
 *
 * Sample usage:
 * <code>
 *  Assert::thatString("Frodo")->startsWith("Fro")->endsWith("do")->contains("rod")->doesNotContain("fro")->hasSize(5);
 *  Assert::thatString("Frodo")->matches('/Fro\w+/');
 *  Assert::thatString("Frodo")->isEqualToIgnoringCase("frodo");
 *  Assert::thatString("Frodo")->isEqualTo("Frodo");
 *  Assert::thatString("Frodo")->isEqualNotTo("asd");
 * </code>
 */

class StringAssert
{
    private $_actual;

    private function __construct($actual)
    {
        $this->_actual = $actual;
    }

    public static function that($actual)
    {
        return new StringAssert($actual);
    }

    public function contains($substring)
    {
        PHPUnit_Framework_Assert::assertContains($substring, $this->_actual);
        return $this;
    }

    public function doesNotContain($substring)
    {
        PHPUnit_Framework_Assert::assertNotContains($substring, $this->_actual);
        return $this;
    }

    public function startsWith($prefix)
    {
        PHPUnit_Framework_Assert::assertStringStartsWith($prefix, $this->_actual);
        return $this;
    }

    public function endsWith($postfix)
    {
        PHPUnit_Framework_Assert::assertStringEndsWith($postfix, $this->_actual);
        return $this;
    }

    public function isEqualToIgnoringCase($string)
    {
        PHPUnit_Framework_Assert::assertEquals($string, $this->_actual, 'Failed asserting that two strings are equal ignoring case.', 0, 10, FALSE, TRUE);
        return $this;
    }

    public function isEqualTo($string)
    {
        PHPUnit_Framework_Assert::assertEquals($string, $this->_actual);
        return $this;
    }

    public function isNotEqualTo($string)
    {
        PHPUnit_Framework_Assert::assertNotEquals($string, $this->_actual);
        return $this;
    }

    public function matches($regex)
    {
        PHPUnit_Framework_Assert::assertRegExp($regex, $this->_actual);
        return $this;
    }

    public function hasSize($length)
    {
        PHPUnit_Framework_Assert::assertEquals($length, mb_strlen($this->_actual));
        return $this;
    }

    public function isNull()
    {
        PHPUnit_Framework_Assert::assertNull($this->_actual);
        return $this;
    }

    public function isNotNull()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->_actual);
        return $this;
    }
} 