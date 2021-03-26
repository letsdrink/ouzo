<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

class SelectColumnCallback
{
    private ?string $prevTable = null;

    public function __invoke(array $matches): string
    {
        $table = $matches[1];

        if ($table != $this->prevTable) {
            $first = !$this->prevTable;
            $this->prevTable = $table;
            $result = "{$table}.*";
            return $first ? $result : ", {$result}";
        }
        return "";
    }
}
