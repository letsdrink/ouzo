<?php
namespace Ouzo\Tests;

/**
 * Fluent custom array assertion to simplify your tests.
 *
 * Sample usage:
 * <code>
 *  $animals = array('cat', 'dog', 'pig');
 *  Assert::thatArray($animals)->hasSize(3)->contains('cat');
 *  Assert::thatArray($animals)->containsOnly('pig', 'dog', 'cat');
 *  Assert::thatArray($animals)->containsExactly('cat', 'dog', 'pig');
 * </code>
 */
class Assert
{

    private $_actual;
    private $_actualString;

    private function __construct(array $actual)
    {
        $this->_actual = $actual;
        $this->_actualString = Objects::toString($actual);
    }

    public static function thatArray(array $actual)
    {
        return ArrayAssert::that($actual);
    }
}