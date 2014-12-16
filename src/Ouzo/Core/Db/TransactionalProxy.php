<?php

namespace Ouzo\Db;

use Ouzo\Db;

class TransactionalProxy
{
    private $_object;

    public function __construct($object)
    {
        $this->_object = $object;
    }

    public static function newInstance($object)
    {
        return new TransactionalProxy($object);
    }

    public function __call($name, $arguments)
    {
        $object = $this->_object;
        return Db::getInstance()->runInTransaction(function () use ($object, $name, $arguments) {
            return call_user_func_array(array($object, $name), $arguments);
        });
    }
}
