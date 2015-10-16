<?php
use Application\Model\Test\Product;
use Ouzo\Db\BatchInserter;
use Ouzo\Tests\DbTransactionalTestCase;


/**
 * Class BatchInserterTest
 * @group postgres
 */
class BatchInserterTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldBuildBatchInsertSql()
    {
        //given
        $inserter = new BatchInserter();

        $inserter->add(new Product(array('name' => 'a', 'position' => 1)));
        $inserter->add(new Product(array('name' => 'b', 'position' => 2)));
        $inserter->add(new Product(array('name' => 'c', 'position' => 3)));

        //when
        $inserter->execute();

        //then
        $this->assertNotNull(Product::where(array('name' =>'a'))->fetch());
        $this->assertNotNull(Product::where(array('name' =>'b'))->fetch());
        $this->assertNotNull(Product::where(array('name' =>'c'))->fetch());
    }
}
