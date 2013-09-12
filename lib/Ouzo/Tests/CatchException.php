<?php
namespace Ouzo\Tests;

use Exception;
use PHPUnit_Framework_Assert;

class CatchException
{
    private $object;
    private static $exception;

    function __construct($object)
    {
        $this->object = $object;
    }

    public static function when($object)
    {
        self::$exception = null;
        return new CatchException($object);
    }

    public static function assertThat()
    {
        return new CatchExceptionAssert(self::$exception);
    }

    public function __call($method, $args)
    {
        try {
            call_user_func_array(array($this->object, $method), $args);
        } catch (Exception $exception) {
            self::$exception = $exception;
        }
    }
}

class CatchExceptionAssert
{
    private $exception;

    function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function isInstanceOf($exception)
    {
        PHPUnit_Framework_Assert::assertInstanceOf($exception, $this->exception);
    }

    public function notCaught()
    {
        PHPUnit_Framework_Assert::assertNull($this->exception);
    }
}