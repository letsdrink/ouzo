<?php
namespace Ouzo\Restriction;

use BadMethodCallException;

abstract class NoValueRestriction extends Restriction
{
    public function getValues()
    {
        throw new BadMethodCallException('This type of restriction has no value');
    }
}
