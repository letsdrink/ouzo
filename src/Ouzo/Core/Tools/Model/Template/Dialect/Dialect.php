<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template\Dialect;

class Dialect
{
    private $_tableName;

    public function __construct($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function primaryKey()
    {
        return '';
    }

    public function sequence()
    {
        return '';
    }

    public function tableName()
    {
        return $this->_tableName;
    }

    public function columns()
    {
        return [];
    }
}
