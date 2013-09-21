<?php
namespace Ouzo\Tests;

use Ouzo\Utilities\Objects;
use PHPUnit_Framework_ComparisonFailure;
use PHPUnit_Framework_ExpectationFailedException;

class ArrayAssert
{

    private $_actual;
    private $_actualString;

    private function __construct(array $actual)
    {
        $this->_actual = $actual;
        $this->_actualString = Objects::toString($actual);
    }

    public static function that(array $actual)
    {
        return new ArrayAssert($actual);
    }

    public function contains()
    {
        $this->isNotNull();

        $elements = func_get_args();
        $nonExistingElements = $this->_findNonExistingElements($elements);

        if (!empty($nonExistingElements)) {
            $nonExistingString = Objects::toString($nonExistingElements);
            $this->fail("Cannot find expected {$nonExistingString} in actual {$this->_actualString}", $nonExistingElements);
        }
        return $this;
    }

    public function containsOnly()
    {
        $this->isNotNull();

        $elements = func_get_args();
        $found = sizeof($elements) - sizeof($this->_findNonExistingElements($elements));

        $elementsString = Objects::toString($elements);
        if (sizeof($elements) > sizeof($this->_actual) || sizeof($this->_actual) > $found) {
            $this->fail("Not all of expected $elementsString were found in actual {$this->_actualString}", $elements);
        }
        if (sizeof($elements) < sizeof($this->_actual) || sizeof($this->_actual) < $found) {
            $this->fail("There are more in expected $elementsString than in actual {$this->_actualString}", $elements);
        }
        return $this;
    }

    public function containsExactly()
    {
        $this->isNotNull();

        $elements = func_get_args();
        $found = 0;
        $min = min(sizeof($this->_actual), sizeof($elements));
        for ($i = 0; $i < $min; $i++) {
            if ($this->_actual[$i] == $elements[$i]) {
                $found++;
            }
        }

        if (sizeof($elements) != $found || sizeof($this->_actual) != $found) {
            $elementsString = Objects::toString($elements);
            $this->fail("Elements from expected $elementsString were not found in actual {$this->_actualString} or have different order", $elements);
        }
        return $this;
    }

    public function hasSize($expectedSize)
    {
        $this->isNotNull();

        $actualSize = sizeof($this->_actual);
        if ($actualSize != $expectedSize) {
            $this->fail("Expected size $expectedSize, but is $actualSize", $expectedSize);
        }
        return $this;
    }

    private function fail($description, $expected = null)
    {
        throw new PHPUnit_Framework_ExpectationFailedException(
            $description,
            new PHPUnit_Framework_ComparisonFailure($expected, $this->_actual, Objects::toString($expected), $this->_actualString)
        );
    }

    public function isNotNull()
    {
        if ($this->_actual === null) {
            $this->fail("Object is null");
        }
        return $this;
    }

    public function isEmpty()
    {
        $this->isNotNull();

        if (!empty($this->_actual)) {
            $this->fail("Object should be empty, but is: {$this->_actualString}");
        }
        return $this;
    }

    public function isNotEmpty()
    {
        $this->isNotNull();

        if (empty($this->_actual)) {
            $this->fail("Object is empty");
        }
        return $this;
    }

    private function _findNonExistingElements($elements)
    {
        $nonExistingElements = array();
        foreach ($elements as $element) {
            if (!in_array($element, $this->_actual)) {
                $nonExistingElements[] = $element;
            }
        }
        return $nonExistingElements;
    }
}