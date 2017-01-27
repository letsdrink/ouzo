<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\RelationFactory;

class RelationFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSetDefaultLocalKeyForBelongsTo()
    {
        // when
        $relation = RelationFactory::create('belongsTo', 'product', array('class' => 'Test\Product'), 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('product_id', $relation->getLocalKey());
    }

    /**
     * @test
     */
    public function shouldSetDefaultLocalKeyForBelongsToForShortVersion()
    {
        // when
        $relation = RelationFactory::create('belongsTo', 'product', 'Test\Product', 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('product_id', $relation->getLocalKey());
    }

    /**
     * @test
     */
    public function shouldSetDefaultLocalKeyForHasOne()
    {
        // when
        $relation = RelationFactory::create('hasOne', 'product', array('class' => 'Test\Product'), 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('primary_id', $relation->getLocalKey());
        $this->assertEquals('user_id', $relation->getForeignKey());
    }

    /**
     * @test
     */
    public function shouldSetDefaultLocalKeyForHasMany()
    {
        // when
        $relation = RelationFactory::create('hasMany', 'product', array('class' => 'Test\Product'), 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('primary_id', $relation->getLocalKey());
        $this->assertEquals('user_id', $relation->getForeignKey());
    }
}