<?php
namespace Ouzo\Tests;

use Ouzo\Db;
use PHPUnit_Framework_TestCase;

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