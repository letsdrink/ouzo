<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class SelectColumnCallback
{
    /** @var string */
    private $prevTable;

    /**
     * @param array $matches
     * @return string
     */
    public function __invoke($matches)
    {
        $table = $matches[1];

        if ($table != $this->prevTable) {
            $first = !$this->prevTable;
            $this->prevTable = $table;
            $result = "$table.*";
            return $first ? $result : ", $result";
        }
        return "";
    }
}
