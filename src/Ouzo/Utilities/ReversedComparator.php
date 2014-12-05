<?php

namespace Ouzo\Utilities;

class ReversedComparator
{
    private $comparator;

    function __construct($comparator)
    {
        $this->comparator = $comparator;
    }

    function __invoke($lhs, $rhs)
    {
        $comparator = $this->comparator;
        return -1 * $comparator($lhs, $rhs);
    }
}