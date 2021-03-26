<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Restriction\Between;
use Ouzo\Restriction\BetweenRestriction;
use Ouzo\Restriction\EqualToRestriction;
use Ouzo\Restriction\GreaterOrEqualToRestriction;
use Ouzo\Restriction\GreaterThanRestriction;
use Ouzo\Restriction\IsInRestriction;
use Ouzo\Restriction\IsNotInRestriction;
use Ouzo\Restriction\IsNotNullRestriction;
use Ouzo\Restriction\IsNullRestriction;
use Ouzo\Restriction\LessOrEqualToRestriction;
use Ouzo\Restriction\LessThanRestriction;
use Ouzo\Restriction\LikeRestriction;
use Ouzo\Restriction\NotEqualToRestriction;
use Ouzo\Restriction\RegexpRestriction;

class Restrictions
{
    public static function equalTo(mixed $value): EqualToRestriction
    {
        return new EqualToRestriction($value);
    }

    public static function notEqualTo(mixed $value): NotEqualToRestriction
    {
        return new NotEqualToRestriction($value);
    }

    public static function like(mixed $value): LikeRestriction
    {
        return new LikeRestriction($value);
    }

    public static function greaterThan(mixed $value): GreaterThanRestriction
    {
        return new GreaterThanRestriction($value);
    }

    public static function lessThan(mixed $value): LessThanRestriction
    {
        return new LessThanRestriction($value);
    }

    public static function greaterOrEqualTo(mixed $value): GreaterOrEqualToRestriction
    {
        return new GreaterOrEqualToRestriction($value);
    }

    public static function lessOrEqualTo(mixed $value): LessOrEqualToRestriction
    {
        return new LessOrEqualToRestriction($value);
    }

    public static function between(mixed $value1, mixed $value2, int $betweenMode = Between::INCLUSIVE): BetweenRestriction
    {
        return new BetweenRestriction($value1, $value2, $betweenMode);
    }

    public static function isNull(): IsNullRestriction
    {
        return new IsNullRestriction();
    }

    public static function isNotNull(): IsNotNullRestriction
    {
        return new IsNotNullRestriction();
    }

    public static function isNotIn(array $values): IsNotInRestriction
    {
        return new IsNotInRestriction($values);
    }

    public static function isIn(array $values): IsInRestriction
    {
        return new IsInRestriction($values);
    }

    public static function regexp(string $value): RegexpRestriction
    {
        return new RegexpRestriction($value);
    }
}
