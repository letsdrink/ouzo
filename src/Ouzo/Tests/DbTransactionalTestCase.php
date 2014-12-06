<?php
namespace Ouzo\Tests;

use Ouzo\Db;
use Ouzo\Utilities\Cache;
use PHPUnit_Framework_TestCase;

class DbTransactionalTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Cache::clear();
        Db::getInstance()->beginTransaction();
    }

    public function tearDown()
    {
        Cache::clear();
        Db::getInstance()->rollbackTransaction();
    }
}
