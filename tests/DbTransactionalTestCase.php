<?php
namespace Thulium\Tests;

use PHPUnit_Framework_TestCase;
use Thulium\Db;

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