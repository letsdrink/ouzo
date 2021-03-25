<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Db;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Iterator\BatchingIterator;

class StatementIteratorTest extends DbTransactionalTestCase
{

    /**
     * @test
     */
    public function shouldFetchIteratorWrappedWithBatching()
    {
        // given
        $numberOfItems = 100;
        $chunkSize = 20;

        for ($i = 0; $i < $numberOfItems; $i++) {
            Product::create(['name' => sprintf('p%03d', $i)]);
        }

        // when
        $iterator = new BatchingIterator(Db::getInstance()->query('SELECT * FROM products ORDER BY name ASC')->fetchIterator(), $chunkSize);

        // then
        $batches = 0;
        $items = 0;
        foreach ($iterator as $products) {
            foreach ($products as $product) {
                $this->assertEquals(sprintf('p%03d', $items), $product['name'], "Product name $i does not match");
                $items++;
            }
            $batches++;
        }
        $this->assertEquals($numberOfItems, $items);
        $this->assertEquals($numberOfItems, $chunkSize * $batches);
    }
}