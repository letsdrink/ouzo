<?php

namespace Ouzo\Utilities\Iterator;


use Iterator;

class TransformingIterator extends ForwardingIterator
{
    private $function;

    public function __construct(Iterator $iterator, $function)
    {
        parent::__construct($iterator);
        $this->function = $function;
    }

    public function current()
    {
        return call_user_func($this->function, $this->iterator->current());
    }
}