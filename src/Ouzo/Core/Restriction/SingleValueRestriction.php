<?php
namespace Ouzo\Restriction;

abstract class SingleValueRestriction extends Restriction
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValues()
    {
        return $this->value;
    }
}
