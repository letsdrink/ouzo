<?php

namespace Ouzo\Config\Inject;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Value
{
    public function __construct(private string $selector)
    {
    }

    public function getSelector(): string
    {
        return $this->selector;
    }
}
