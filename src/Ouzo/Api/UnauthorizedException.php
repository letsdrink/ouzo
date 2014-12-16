<?php
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\OuzoException;

class UnauthorizedException extends OuzoException
{
    const HTTP_CODE = 401;

    public function __construct($errors, $headers = array())
    {
        parent::__construct(self::HTTP_CODE, $errors, $headers);
    }
}
