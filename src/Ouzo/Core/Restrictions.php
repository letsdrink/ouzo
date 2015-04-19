<?php
namespace Ouzo;

use Ouzo\Restriction\Between;
use Ouzo\Restriction\BetweenRestriction;
use Ouzo\Restriction\EqualToRestriction;
use Ouzo\Restriction\GreaterOrEqualToRestriction;
use Ouzo\Restriction\GreaterThanRestriction;
use Ouzo\Restriction\LessOrEqualToRestriction;
use Ouzo\Restriction\LessThanRestriction;
use Ouzo\Restriction\LikeRestriction;
use Ouzo\Restriction\NotEqualToRestriction;

class Restrictions
{
    public static function equalTo($value)
    {
        return new EqualToRestriction($value);
    }

    public static function notEqualTo($value)
    {
        return new NotEqualToRestriction($value);
    }

    public static function like($value)
    {
        return new LikeRestriction($value);
    }

    public static function greaterThan($value)
    {
        return new GreaterThanRestriction($value);
    }

    public static function lessThan($value)
    {
        return new LessThanRestriction($value);
    }

    public static function greaterOrEqualTo($value)
    {
        return new GreaterOrEqualToRestriction($value);
    }

    public static function lessOrEqualTo($value)
    {
        return new LessOrEqualToRestriction($value);
    }

    public static function between($value1, $value2, $betweenMode = Between::INCLUSIVE)
    {
        return new BetweenRestriction($value1, $value2, $betweenMode);
    }
}
