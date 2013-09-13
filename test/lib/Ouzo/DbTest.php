<?php
use Model\Category;
use Ouzo\Db;
use Ouzo\Tests\DbTransactionalTestCase;

class Sample
{
    public function callMethod()
    {
        return 'OK';
    }
}

class DbTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldRunFunctionInTransaction()
    {
        //when
        $return = Db::getInstance()->runInTransaction(array(new Sample(), 'callMethod'));

        //then
        $this->assertEquals('OK', $return);
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