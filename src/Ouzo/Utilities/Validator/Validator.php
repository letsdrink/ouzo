<?php
namespace Ouzo\Utilities\Validator;

class Validator
{
    public static function isTrue($value, $message = '')
    {
        if ($value !== true) {
            throw new ValidatorException($message);
        }
        return true;
    }

    public static function isEmail($value, $message = '')
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidatorException($message);
        }
        return true;
    }

    public static function isNotNull($value, $message = '')
    {
        if ($value === null) {
            throw new ValidatorException($message);
        }
        return true;
    }
}