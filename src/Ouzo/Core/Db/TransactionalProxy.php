<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db;

class TransactionalProxy
{
    /** @var mixed */
    private $object;

    /**
     * @param mixed $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * @param mixed $object
     * @return TransactionalProxy
     */
    public static function newInstance($object)
    {
        return new TransactionalProxy($object);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $object = $this->object;
        return Db::getInstance()->runInTransaction(function () use ($object, $name, $arguments) {
            return call_user_func_array([$object, $name], $arguments);
        });
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $function = $this->object;
        $arguments = func_get_args();
        return Db::getInstance()->runInTransaction(function () use ($function, $arguments) {
            return call_user_func_array($function, $arguments);
        });
    }
}
