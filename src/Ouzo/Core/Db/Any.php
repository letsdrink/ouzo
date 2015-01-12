<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class Any
{
    private $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public static function of(array $conditions)
    {
        return new self($conditions);
    }
}
