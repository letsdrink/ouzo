<?php
namespace Ouzo\Tests;
use Ouzo\Model;

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
 *
 *  Assert::thatModel(new User(['name' => 'bob']))->hasSameAttributesAs(new User(['name' => 'bob']));
 *
 *  Assert::thatString("Frodo")->startsWith("Fro")->endsWith("do")->contains("rod")->doesNotContain("fro")->hasSize(5)->matches('/Fro\w+/');
 * </code>
 */
class Assert
{
    public static function thatArray(array $actual)
    {
        return ArrayAssert::that($actual);
    }

    public static function thatModel(Model $actual)
    {
        return ModelAssert::that($actual);
    }

    public static function thatSession()
    {
        return ArrayAssert::that(isset($_SESSION) ? $_SESSION : array());
    }

    public static function thatString($string)
    {
        return StringAssert::that($string);
    }

}