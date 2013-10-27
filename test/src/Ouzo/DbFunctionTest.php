<?php

namespace Ouzo;

use Model\Test\Category;
use Ouzo\Tests\DbTransactionalTestCase;

class DbFunctionTest extends DbTransactionalTestCase
{

    public function setUp() {
        $driver = Config::getValue('db', 'driver');
        if ($driver == 'sqlite') {
            $this->markTestSkipped('This test is not for SQLite database.');
        }

        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldCallDbFunction()
    {
            //given
            $category = Category::create(array('name' => 'test'));

            //when
            $name = $category->getName('test');

            //then
            $this->assertEquals('test', $name);
    }

}