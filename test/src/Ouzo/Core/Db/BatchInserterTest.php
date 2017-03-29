<?php
use Application\Model\Test\Order;
use Application\Model\Test\OrderProduct;
use Application\Model\Test\Product;
use Ouzo\Db\BatchInserter;
use Ouzo\Tests\DbTransactionalTestCase;

class BatchInserterTest extends DbTransactionalTestCase
{
    /**
     * @group postgres
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
        $this->assertNotNull(Product::where(['name' => 'a'])->fetch());
        $this->assertNotNull(Product::where(['name' => 'b'])->fetch());
        $this->assertNotNull(Product::where(['name' => 'c'])->fetch());
    }

    /**
     * @group postgres
     * @test
     */
    public function shouldBatchInsertWithoutReturning()
    {
        //given
        $product1 = Product::create(['name' => 'product1']);
        $order1 = Order::create(['name' => 'order1']);
        $product2 = Product::create(['name' => 'product2']);
        $order2 = Order::create(['name' => 'order2']);
        $inserter = new BatchInserter();
        $inserter->add(new OrderProduct(['id_order' => $order1->getId(), 'id_product' => $product1->getId()]));
        $inserter->add(new OrderProduct(['id_order' => $order2->getId(), 'id_product' => $product2->getId()]));

        //when
        $inserter->execute();

        //then
        $this->assertNotNull(OrderProduct::where(['id_order' => $order1->getId()])->fetch());
        $this->assertNotNull(OrderProduct::where(['id_order' => $order2->getId()])->fetch());
    }

    /**
     * @test
     * @group postgres
     */
    public function shouldBatchInsertWhenModelHasFetchedRelation()
    {
        //given
        $product1 = new Product(['name' => 'product1']);
        $product2 = new Product(['name' => 'product2']);
        $product1->category;
        $inserter = new BatchInserter();
        $inserter->add($product1);
        $inserter->add($product2);

        //when
        $inserter->execute();

        //then
        $this->assertNotNull(Product::where(['name' => 'product1'])->fetch());
        $this->assertNotNull(Product::where(['name' => 'product2'])->fetch());
    }
}
