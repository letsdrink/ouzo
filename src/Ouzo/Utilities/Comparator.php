<?php

namespace Ouzo\Utilities;

class Comparator
{

    /**
     * @param mixed ...
     * @return \Ouzo\Utilities\CompoundComparator
     */
    public static function compound()
    {
        return new CompoundComparator(func_get_args());
    }

    public static function compareBy($expression)
    {
        return new EvaluatingComparator(Functions::extractExpression($expression));
    }

    public static function reverse($comparator)
    {
        return new ReversedComparator($comparator);
    }
}