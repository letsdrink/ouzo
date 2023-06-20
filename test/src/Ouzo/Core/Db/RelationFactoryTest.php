<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\RelationFactory;
use PHPUnit\Framework\TestCase;

class RelationFactoryTest extends TestCase
{
    #[Test]
    public function shouldSetDefaultLocalKeyForBelongsTo()
    {
        // when
        $relation = RelationFactory::create('belongsTo', 'product', ['class' => 'Test\Product'], 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('product_id', $relation->getLocalKey());
    }

    #[Test]
    public function shouldSetDefaultLocalKeyForBelongsToForShortVersion()
    {
        // when
        $relation = RelationFactory::create('belongsTo', 'product', 'Test\Product', 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('product_id', $relation->getLocalKey());
    }

    #[Test]
    public function shouldSetDefaultLocalKeyForHasOne()
    {
        // when
        $relation = RelationFactory::create('hasOne', 'product', ['class' => 'Test\Product'], 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('primary_id', $relation->getLocalKey());
        $this->assertEquals('user_id', $relation->getForeignKey());
    }

    #[Test]
    public function shouldSetDefaultLocalKeyForHasMany()
    {
        // when
        $relation = RelationFactory::create('hasMany', 'product', ['class' => 'Test\Product'], 'primary_id', '\Model\User');

        // then
        $this->assertEquals('Test\Product', $relation->getClass());
        $this->assertEquals('primary_id', $relation->getLocalKey());
        $this->assertEquals('user_id', $relation->getForeignKey());
    }
}