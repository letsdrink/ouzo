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

        $inserter->add(new Product(['name' => 'a', 'position' => 1]));
        $inserter->add(new Product(['name' => 'b', 'position' => 2]));
        $inserter->add(new Product(['name' => 'c', 'position' => 3]));

        //when
        $inserter->execute();

        //then
        $this->assertNotNull(Product::where(['name' =>'a'])->fetch());
        $this->assertNotNull(Product::where(['name' =>'b'])->fetch());
        $this->assertNotNull(Product::where(['name' =>'c'])->fetch());
    }
}
