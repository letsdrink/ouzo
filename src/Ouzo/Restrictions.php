<?php

namespace Ouzo;


use Ouzo\Restriction\EqualToRestriction;

class Restrictions
{

    public static function equalTo($value)
    {
        return new EqualToRestriction($value);
    }
} 