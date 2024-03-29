<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests\Mock;

class AnyArgument implements ArgumentMatcher
{
    public function __toString(): string
    {
        return "any";
    }

    public function matches(mixed $argument): bool
    {
        return true;
    }
}
