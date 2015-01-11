<?php
namespace Ouzo\Restriction;

use BadMethodCallException;

abstract class NonValueRestriction extends Restriction
{
    public function getValues()
    {
        throw new BadMethodCallException('This type of restriction has not value');
    }
}
