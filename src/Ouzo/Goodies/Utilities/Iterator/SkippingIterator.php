<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Iterator;

use Iterator;

class SkippingIterator extends ForwardingIterator
{
    private bool $skipped = false;

    #[Override]
    public function __construct(Iterator $iterator, private int $skipCount)
    {
        parent::__construct($iterator);
    }

    #[Override]
    public function valid(): bool
    {
        if (!$this->skipped) {
            for ($i = 0; $i < $this->skipCount; ++$i) {
                $this->iterator->next();
            }
            $this->skipped = true;
        }
        return parent::valid();
    }

    #[Override]
    public function rewind(): void
    {
        $this->skipped = false;
        $this->iterator->rewind();
    }
}
