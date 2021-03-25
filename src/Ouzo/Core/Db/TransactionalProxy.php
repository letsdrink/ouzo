<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db;

class TransactionalProxy
{
    public function __construct(private mixed $object)
    {
    }

    public static function newInstance(mixed $object): TransactionalProxy
    {
        return new TransactionalProxy($object);
    }

    public function __call(string $name, array $arguments): mixed
    {
        return Db::getInstance()->runInTransaction(fn() => $this->object->$name(...$arguments));
    }

    public function __invoke(mixed ...$arguments): mixed
    {
        return Db::getInstance()->runInTransaction(fn() => ($this->object)(...$arguments));
    }
}
