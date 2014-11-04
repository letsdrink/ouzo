<?php
namespace Ouzo\Utilities\Validator;

class Validate
{
    public static function isTrue($value, $message = '')
    {
        if ($value !== true) {
            throw new ValidateException($message);
        }
        return true;
    }

    public static function isEmail($value, $message = '')
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidateException($message);
        }
        return true;
    }

    public static function isNotNull($value, $message = '')
    {
        if ($value === null) {
            throw new ValidateException($message);
        }
        return true;
    }
}