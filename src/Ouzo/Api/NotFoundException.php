<?php
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\OuzoException;

class NotFoundException extends OuzoException
{
    const HTTP_CODE = 404;

    public function __construct($errors)
    {
        parent::__construct(self::HTTP_CODE, $errors);
    }
}