<?php

/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Product;
use Ouzo\Tests\ModelAttributesMatcher;
use PHPUnit\Framework\TestCase;

class ModelAttributesMatcherTest extends TestCase
{
    #[Test]
    public function shouldMatchOnlyModelFields()
    {
        //given
        $model1 = new Product(['name' => 'product', 'other' => 'other1']);
        $model2 = new Product(['name' => 'product', 'other' => 'other2']);

        $matcher = new ModelAttributesMatcher($model1);

        //when
        $result = $matcher->matches($model2);

        //then
        $this->assertTrue($result);
    }

    #[Test]
    public function shouldReturnFalseIfDifferentAttributes()
    {
        //given
        $model1 = new Product(['name' => 'product1']);
        $model2 = new Product(['name' => 'product2']);

        $matcher = new ModelAttributesMatcher($model1);

        //when
        $result = $matcher->matches($model2);

        //then
        $this->assertFalse($result);
    }

    #[Test]
    public function shouldReturnDescription()
    {
        //given
        $model = new Product(['name' => 'product1']);
        $matcher = new ModelAttributesMatcher($model);

        //when
        $description = $matcher->__toString();

        //then
        $attributes = print_r($model->attributes(), true);
        $this->assertEquals("Application\\Model\\Test\\Product with attributes($attributes)", $description);
    }
}
