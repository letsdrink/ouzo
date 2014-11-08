<?php
namespace Ouzo\Tests;

use Exception;
use Ouzo\ExceptionHandling\OuzoException;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_ExpectationFailedException;

class CatchException
{
    private $object;

    private static $exception;

    public function __construct($object)
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

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function isInstanceOf($exception)
    {
        PHPUnit_Framework_Assert::assertInstanceOf($exception, $this->exception);
        return $this;
    }

    public function isEqualTo($exception)
    {
        PHPUnit_Framework_Assert::assertEquals($exception, $this->exception);
        return $this;
    }

    public function notCaught()
    {
        PHPUnit_Framework_Assert::assertNull($this->exception);
        return $this;
    }

    public function hasMessage($message)
    {
        if ($this->exception instanceof OuzoException) {
            Assert::thatArray($this->exception->getErrors())->onProperty('message')->contains($message);
        } else if ($this->exception instanceof Exception) {
            PHPUnit_Framework_Assert::assertEquals($message, $this->exception->getMessage());
        } else {
            throw new PHPUnit_Framework_ExpectationFailedException('Message not contains in exceptions');
        }
        return $this;
    }
}