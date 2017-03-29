<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\Strings;

class QueryHumanizer
{
    /**
     * @param $sql
     * @return string
     */
    public static function humanize($sql)
    {
        if (Strings::endsWith($sql, ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT . ' */')) {
            $sql = Strings::removeSuffix($sql, ' /* ' . ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT . ' */');
            return preg_replace_callback('/SELECT .*? FROM/', function ($matches) {
                return preg_replace_callback('/(\w+)\.(\w+)( AS \w+)?(, )?/', new SelectColumnCallback(), $matches[0]);
            }, $sql);
        }
        return $sql;
    }
}
