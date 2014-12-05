<?php

namespace Ouzo\Utilities;

class Comparator
{

    /**
     * Combines comparators into one resolving order using first comparator and resolving conflicts using tie breakers.
     * Second provided comparator is first tie breaker, third is second tie breaker and so on.
     *
     * @param mixed ...
     * @return comparator
     */
    public static function compound()
    {
        return new CompoundComparator(func_get_args());
    }

    /**
     * Returns comparator which compares objects by using values computed using given expression.
     * Expression should comply with format accepted by <code>Functions::extractExpression</code>.
     * Comparator returns an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.
     *
     * @param $expression
     * @return comparator
     */
    public static function compareBy($expression)
    {
        return new EvaluatingComparator(Functions::extractExpression($expression));
    }

    /**
     * Returns comparator according to which order between element is reversed.
     *
     * @param $comparator
     * @return comparator
     */
    public static function reverse($comparator)
    {
        return new ReversedComparator($comparator);
    }
}