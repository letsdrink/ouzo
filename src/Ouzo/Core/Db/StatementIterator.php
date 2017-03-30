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
    /** @var PDOStatement */
    private $statement;
    /** @var IteratorIterator */
    private $iterator;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
        $this->iterator = new IteratorIterator($statement);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $valid = $this->iterator->valid();
        if (!$valid) {
            $this->closeCursor();
        }
        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * @return void
     */
    public function closeCursor()
    {
        Logger::getLogger(__CLASS__)->info("Closing cursor");
        $this->statement->closeCursor();
    }
}
