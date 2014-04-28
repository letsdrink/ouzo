<?php
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\OuzoException;

class ValidationException extends OuzoException
{
    const HTTP_CODE = 400;

    public function __construct($errors)
    {
        parent::__construct(self::HTTP_CODE, $errors);
    }
}