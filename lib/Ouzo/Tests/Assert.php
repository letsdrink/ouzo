<?php
namespace Ouzo\Tests;

use Ouzo\Utilities\Objects;
use PHPUnit_Framework_ComparisonFailure;
use PHPUnit_Framework_ExpectationFailedException;

class Assert
{

    private $_actual;
    private $_actualString;

    private function __construct($actual)
    {
        $this->_actual = $actual;
        $this->_actualString = Objects::toString($actual);
    }

    public static function that($actual)
    {
        return new Assert($actual);
    }

    public function contains()
    {
        $this->isArray();

        $elements = func_get_args();
        foreach ($elements as $element) {
            if (!in_array($element, $this->_actual)) {
                $this->fail("Cannot find $element in {$this->_actualString}", $element);
            }
        }
        return $this;
    }

    public function containsOnly()
    {
        $this->isArray();

        $elements = func_get_args();
        $elementsString = Objects::toString($elements);
        $found = 0;
        foreach ($elements as $element) {
            if (in_array($element, $this->_actual)) {
                $found++;
            }
        }
        if (sizeof($elements) > sizeof($this->_actual) || sizeof($this->_actual) > $found) {
            $this->fail("Not all of $elementsString were found in {$this->_actualString}", $elements);
        }
        if (sizeof($elements) < sizeof($this->_actual) || sizeof($this->_actual) < $found) {
            $this->fail("There more in $elementsString than in {$this->_actualString}", $elements);
        }
        return $this;
    }

    public function containsExactly()
    {
        $this->isArray();

        $elements = func_get_args();
        $elementsString = Objects::toString($elements);
        $found = 0;
        $min = min(sizeof($this->_actual), sizeof($elements));
        for ($i = 0; $i < $min; $i++) {
            if ($this->_actual[$i] == $elements[$i]) {
                $found++;
            }
        }
        if (sizeof($elements) != $found || sizeof($this->_actual) != $found) {
            $this->fail("Elements from $elementsString were not found in {$this->_actualString} or have different order", $elements);
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

    public function notNull()
    {
        if ($this->_actual === null) {
            $this->fail("Object is null");
        }
        return $this;
    }

    public function isArray()
    {
        $this->notNull();

        if (!is_array($this->_actual)) {
            $this->fail("Object is not an array");
        }
        return $this;
    }

    public function isEmpty()
    {
        $this->isArray();

        if (!empty($this->_actual)) {
            $this->fail("Object should be empty, but is: {$this->_actualString}");
        }
        return $this;
    }

    public function isNotEmpty()
    {
        $this->isArray();

        if (empty($this->_actual)) {
            $this->fail("Object is empty");
        }
        return $this;
    }
}