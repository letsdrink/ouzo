<?php

namespace Ouzo;


use Ouzo\Restriction\EqualToRestriction;
use Ouzo\Restriction\ILikeRestriction;
use Ouzo\Restriction\LikeRestriction;

class Restrictions
{

    public static function equalTo($value)
    {
        return new EqualToRestriction($value);
    }

    public static function like($value)
    {
        return new LikeRestriction($value);
    }

    public static function iLike($value)
    {
        return new ILikeRestriction($value);
    }
}