<?php

use Model\Test\Product;
use Ouzo\Restrictions;
use Ouzo\Tests\DbTransactionalTestCase;

class RestrictionsTest extends DbTransactionalTestCase
{

    /**
     * @test
     */
    public function shouldReturnResultUsingEqualToRestriction()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::equalTo('tech')))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnResultUsingLikeRestriction()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::like('te%')))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnNothingUsingEqualToRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::equalTo('te')))->fetch();

        //then
        $this->assertNull($loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnNothingUsingLikeRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::like('te')))->fetch();

        //then
        $this->assertNull($loadedProduct);
    }
}