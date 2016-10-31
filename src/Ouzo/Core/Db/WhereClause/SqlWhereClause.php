<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class SqlWhereClause extends WhereClause
{
    /** @var string */
    private $sql;

    /** @var array */
    private $values;

    public function __construct($sql, $parameters = array())
    {
        $this->sql = $sql;
        $this->values = $parameters === NULL ? array(null) : Arrays::toArray($parameters);
    }

    public function toSql()
    {
        return stripos($this->sql, 'OR') ? '(' . $this->sql . ')' : $this->sql;
    }

    public function getParameters()
    {
        return $this->values;
    }

    public function isEmpty()
    {
        return Strings::isBlank($this->sql);
    }
}
