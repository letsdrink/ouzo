<?php

namespace Ouzo\Db;

use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Utilities\Arrays;

class PDOExceptionExtractor
{
    public static function getException($errorInfo, $querySql)
    {
        $exceptionClassName = DialectFactory::create()->getExceptionForError($errorInfo);
        return new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
            $querySql,
            self::errorMessageFromErrorInfo($errorInfo),
            self::_errorCodesFromErrorInfo($errorInfo)
        ));
    }

    public static function errorMessageFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 2);
    }

    private static function _errorCodesFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0) . " " . Arrays::getValue($errorInfo, 1);
    }
} 