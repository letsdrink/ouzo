<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class QueryType
{
    public static int $SELECT = 1;
    public static int $COUNT = 2;
    public static int $DELETE = 3;
    public static int $UPDATE = 4;
    public static int $INSERT = 5;
    public static int $UPSERT = 6;
    public static int $INSERT_OR_DO_NOTHING = 7;
}
