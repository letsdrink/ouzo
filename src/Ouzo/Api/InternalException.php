<?php
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\OuzoException;

class InternalException extends OuzoException
{
    const HTTP_CODE = 500;

    public function __construct($errors, $headers = array())
    {
        parent::__construct(self::HTTP_CODE, $errors, $headers);
    }
}