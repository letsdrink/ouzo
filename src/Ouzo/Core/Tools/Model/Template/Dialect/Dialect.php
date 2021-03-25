<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template\Dialect;

class Dialect
{
    public function __construct(private string $tableName)
    {
    }

    public function primaryKey(): string
    {
        return '';
    }

    public function sequence(): string
    {
        return '';
    }

    public function tableName(): string
    {
        return $this->tableName;
    }

    /** @return string[] */
    public function columns(): array
    {
        return [];
    }
}
