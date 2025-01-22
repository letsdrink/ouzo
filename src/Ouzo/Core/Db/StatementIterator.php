<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Iterator;
use IteratorIterator;
use Ouzo\Logger\Logger;
use PDOStatement;

class StatementIterator implements Iterator
{
    private IteratorIterator $iterator;

    public function __construct(private PDOStatement $statement)
    {
        $this->iterator = new IteratorIterator($statement);
    }

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): float|bool|int|string|null
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        $valid = $this->iterator->valid();
        if (!$valid) {
            $this->closeCursor();
        }
        return $valid;
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function closeCursor(): void
    {
        Logger::getLogger(__CLASS__)->asLoggerInterface()->info("Closing cursor");
        $this->statement->closeCursor();
    }
}
