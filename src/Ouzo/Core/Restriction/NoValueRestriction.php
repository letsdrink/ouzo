<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

abstract class NoValueRestriction extends Restriction
{
    public function getValues(): array
    {
        return [];
    }
}
