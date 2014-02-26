<?php
namespace Ouzo\Tests;

use Ouzo\Session;
use Ouzo\Utilities\Arrays;

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

    public static function thatSession($namespace = Session::DEFAULT_NAMESPACE)
    {
        return ArrayAssert::that(Arrays::getValue($_SESSION, $namespace, array()));
    }
}