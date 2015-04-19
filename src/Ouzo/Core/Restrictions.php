<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Restriction\Between;
use Ouzo\Restriction\BetweenRestriction;
use Ouzo\Restriction\EqualToRestriction;
use Ouzo\Restriction\GreaterOrEqualToRestriction;
use Ouzo\Restriction\GreaterThanRestriction;
use Ouzo\Restriction\IsNotInRestriction;
use Ouzo\Restriction\IsNotNullRestriction;
use Ouzo\Restriction\IsNullRestriction;
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

    public static function isNull()
    {
        return new IsNullRestriction();
    }

    public static function isNotNull()
    {
        return new IsNotNullRestriction();
    }

    public static function isNotIn(array $values)
    {
        return new IsNotInRestriction($values);
    }
}
