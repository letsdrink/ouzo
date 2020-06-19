<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Backtrace
{
    private const OUZO_PACKAGE_NAME = 'letsdrink/ouzo';

    public static function getCallingClass(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $line = null;
        $file = null;

        foreach ($trace as $traceItem) {
            if ($line) {
                return Arrays::getValue($traceItem, 'class', $file) . ':' . $line;
            }
            $file = Arrays::getValue($traceItem, 'file');
            if ($file && !Strings::contains($file, self::OUZO_PACKAGE_NAME)) {
                $line = Arrays::getValue($traceItem, 'line');
            }
        }
        return $file . ':' . $line;
    }
}