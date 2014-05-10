<?php
namespace Ouzo\Tests;

use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use PHPUnit_Framework_Assert;

class ModelAssert
{
    /**
     * @var Model
     */
    private $_actual;

    private function __construct(Model $actual)
    {
        $this->_actual = $actual;
    }

    public static function that(Model $actual)
    {
        return new ModelAssert($actual);
    }

    /**
     * Compares all attributes. If one model has loaded a relation and other has not, they are considered not equal.
     */
    public function isEqualTo(Model $expected)
    {
        $this->_assertSameType($expected);
        PHPUnit_Framework_Assert::assertEquals($expected->attributes(), $this->_actual->attributes(), 'Models have different attributes ');
    }

    /**
     * Compares only attributes listed in Models fields!
     */
    public function hasSameAttributesAs(Model $expected)
    {
        $this->_assertSameType($expected);
        $this->_assertSamePersistentAttributes($expected);
    }

    private function _assertSameType(Model $expected)
    {
        PHPUnit_Framework_Assert::assertEquals(get_class($expected), get_class($this->_actual),
            'Expected object of type ' . $expected->getModelName() . ' but got ' . $this->_actual->getModelName());
    }

    private function _assertSamePersistentAttributes(Model $expected)
    {
        $expectedAttributes = Arrays::filterByAllowedKeys($expected->attributes(), $expected->getFields());
        $actualAttributes = Arrays::filterByAllowedKeys($this->_actual->attributes(), $this->_actual->getFields());

        PHPUnit_Framework_Assert::assertEquals($expectedAttributes, $actualAttributes, 'Models have different attributes ');
    }
}