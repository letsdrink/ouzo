<?php
namespace Ouzo\Restriction;

abstract class Restriction
{
    abstract public function toSql($fieldName);

    abstract public function getValues();
}
