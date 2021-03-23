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
    public static function getException(array $errorInfo, string $querySql): Exception
    {
        $exceptionClassName = DialectFactory::create()->getExceptionForError($errorInfo);
        return new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
            $querySql,
            self::errorMessageFromErrorInfo($errorInfo),
            self::errorCodesFromErrorInfo($errorInfo)
        ));
    }

    public static function errorMessageFromErrorInfo(array $errorInfo): ?string
    {
        return Arrays::getValue($errorInfo, 2);
    }

    private static function errorCodesFromErrorInfo(array $errorInfo): string
    {
        return Arrays::getValue($errorInfo, 0) . " " . Arrays::getValue($errorInfo, 1);
    }
}
