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
 *  Assert::thatArray(array('id' => 123, 'name' => 'john'))->containsKeyAndValue(array('id' => 123));
 * </code>
 */
class Assert
{
    public static function thatArray(array $actual)
    {
        return ArrayAssert::that($actual);
    }
}