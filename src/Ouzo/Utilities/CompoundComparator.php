<?php

namespace Ouzo\Utilities;

class CompoundComparator
{
    /**
     * @var array
     */
    private $comparators;

    function __construct(array $comparators)
    {
        $this->comparators = $comparators;
    }

    function __invoke($lhs, $rhs)
    {
        foreach ($this->comparators as $comparator) {
            $result = $comparator($lhs, $rhs);
            if ($result != 0) {
                return $result;
            }
        }
    }
}