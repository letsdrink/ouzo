<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

abstract class Restriction
{
    abstract public function toSql($fieldName);

    abstract public function getValues();
}
