<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Iterator;
use IteratorIterator;
use Ouzo\Logger\Logger;
use PDOStatement;

class StatementIterator implements Iterator
{

    private $statement;
    private $iterator;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
        $this->iterator = new IteratorIterator($statement);
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        $valid = $this->iterator->valid();
        if (!$valid) {
            $this->closeCursor();
        }
        return $valid;
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    public function closeCursor()
    {
        Logger::getLogger(__CLASS__)->info("Closing cursor");
        $this->statement->closeCursor();
    }
}