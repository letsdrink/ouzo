<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests;

use Ouzo\Db;
use Ouzo\Utilities\Cache;
use PHPUnit_Framework_TestCase;

class DbTransactionalTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Cache::clear();
        if (Db::getInstance()->isConnected()) {
            Db::getInstance()->beginTransaction();
            Db::getInstance()->disableTransactions();
        }
    }

    public function tearDown()
    {
        Cache::clear();
        if (Db::getInstance()->isConnected()) {
            Db::getInstance()->enableTransactions();
            Db::getInstance()->rollbackTransaction();
        }
    }
}
