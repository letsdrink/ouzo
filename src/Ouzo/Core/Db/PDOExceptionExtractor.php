<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Exception;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Utilities\Arrays;

class PDOExceptionExtractor
{
    /**
     * @param array $errorInfo
     * @param string $querySql
     * @return Exception
     */
    public static function getException($errorInfo, $querySql)
    {
        $exceptionClassName = DialectFactory::create()->getExceptionForError($errorInfo);
        return new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
            $querySql,
            self::errorMessageFromErrorInfo($errorInfo),
            self::errorCodesFromErrorInfo($errorInfo)
        ));
    }

    /**
     * @param array $errorInfo
     * @return string|null
     */
    public static function errorMessageFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 2);
    }

    /**
     * @param array $errorInfo
     * @return string
     */
    private static function errorCodesFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0) . " " . Arrays::getValue($errorInfo, 1);
    }
}
