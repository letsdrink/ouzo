<?php

namespace Ouzo\Restriction;


abstract class Restriction {

    public abstract function toSql($fieldName);

    public abstract function getValues();
} 