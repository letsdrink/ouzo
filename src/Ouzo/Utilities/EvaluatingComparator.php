<?php

namespace Ouzo\Utilities;

class EvaluatingComparator
{

    private $toEvaluate;

    function __construct($toEvaluate)
    {
        $this->toEvaluate = $toEvaluate;
    }

    function __invoke($lhs, $rhs)
    {
        $functionToEvaluate = $this->toEvaluate;
        $lhsValue = $functionToEvaluate($lhs);
        $rhsValue = $functionToEvaluate($rhs);
        return $this->compareEvaluated($lhsValue, $rhsValue);
    }

    private function compareEvaluated($lhsValue, $rhsValue)
    {
        if ($lhsValue == $rhsValue) {
            return 0;
        } else {
            return $lhsValue < $rhsValue ? -1 : 1;
        }
    }
}