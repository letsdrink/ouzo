<?php
namespace Ouzo\Tests;

use PHPUnit_Framework_TestCase;
use Ouzo\Db;

class DbTransactionalTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Db::getInstance()->beginTransaction();
    }

    public function tearDown()
    {
        Db::getInstance()->rollbackTransaction();
    }
}