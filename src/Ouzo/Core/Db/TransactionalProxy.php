<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
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
        $object = $this->object;
        return Db::getInstance()->runInTransaction(fn() => call_user_func_array([$object, $name], $arguments));
    }

    public function __invoke(): mixed
    {
        $function = $this->object;
        $arguments = func_get_args();
        return Db::getInstance()->runInTransaction(fn() => call_user_func_array($function, $arguments));
    }
}
