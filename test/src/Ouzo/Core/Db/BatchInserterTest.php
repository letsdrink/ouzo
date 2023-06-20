<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Order;
use Application\Model\Test\OrderProduct;
use Application\Model\Test\Product;
use Ouzo\Db\BatchInserter;
use Ouzo\Db\OnConflict;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class BatchInserterTest extends DbTransactionalTestCase
{
    #[Test]
    #[Group('postgres')]
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

    #[Test]
    #[Group('postgres')]
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

    #[Test]
    #[Group('postgres')]
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

    #[Test]
    #[Group('postgres')]
    public function shouldBatchInsertOnConflictDoNothing()
    {
        //given
        $product = Product::create(['name' => 'product1']);
        $order = Order::create(['name' => 'order1']);
        OrderProduct::create(['id_product' => $product->getId(), 'id_order' => $order->getId()]);

        $orderProduct = new OrderProduct(['id_product' => $product->getId(), 'id_order' => $order->getId()]);
        $inserter = new BatchInserter();
        $inserter->add($orderProduct);
        $inserter->onConflict(OnConflict::doNothing());

        //when
        $inserter->execute();

        //then
        Assert::thatArray(OrderProduct::all())
            ->hasSize(1)
            ->extracting('id_product', 'id_order')
            ->containsOnly([$product->getId(), $order->getId()]);
    }

    #[Test]
    #[Group('postgres')]
    public function shouldBatchInsertOnConflictUpdate()
    {
        //given
        $product = Product::create(['name' => 'product1']);
        $product2 = Product::create(['name' => 'product2']);
        $order = Order::create(['name' => 'order1']);
        OrderProduct::create(['id_product' => $product->getId(), 'id_order' => $order->getId()]);

        $orderProduct = new OrderProduct(['id_product' => $product->getId(), 'id_order' => $order->getId()]);
        $inserter = new BatchInserter();
        $inserter->add($orderProduct);
        $inserter->onConflict(OnConflict::doUpdate(['id_product', 'id_order'], ['id_product' => $product2->getId()]));

        //when
        $inserter->execute();

        //then
        Assert::thatArray(OrderProduct::all())
            ->hasSize(1)
            ->extracting('id_product', 'id_order')
            ->containsOnly([$product2->getId(), $order->getId()]);
    }
}
