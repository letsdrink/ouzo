<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities;

class ClassWithMethodThatTakesPrimitives
{
    public function fun1(int $p1, array $p2, ?TestClass $p3)
    {
    }
}

class ClassWithMethodThatReturnType
{
    public function fun1(int $p1): int
    {
        return $p1;
    }
}

class ClassWithNullReturningMethod
{
    public function fun1(int $p1): ?string
    {
        return null;
    }
}

class ClassWithMixedReturningMethod
{
    public function fun1(mixed $p1): mixed
    {
        return $p1;
    }
}