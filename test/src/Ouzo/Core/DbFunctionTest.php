<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Category;
use Ouzo\Config;
use Ouzo\Tests\DbTransactionalTestCase;

class DbFunctionTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
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
        $category = Category::create(['name' => 'test']);

        //when
        $name = $category->getName('test');

        //then
        $this->assertEquals('test', $name);
    }
}
