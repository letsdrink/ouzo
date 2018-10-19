<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class QueryType
{
    public static $SELECT = 1;
    public static $COUNT = 2;
    public static $DELETE = 3;
    public static $UPDATE = 4;
    public static $INSERT = 5;
    public static $UPSERT = 6;
    public static $INSERT_OR_DO_NOTHING = 7;
}
