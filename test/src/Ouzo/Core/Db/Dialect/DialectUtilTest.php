<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\Dialect\DialectUtil;
use Ouzo\Db\JoinClause;
use Ouzo\Db\WhereClause;
use Ouzo\Tests\Assert;

class DialectUtilTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReplaceTableWithAlias()
    {
        //given
        $joinClause = new JoinClause('products', 'id', 'product_id', 'order_products', 'p', 'LEFT', array(new WhereClause('products.active = true')));

        //when
        $buildJoinQueryPart = DialectUtil::buildJoinQueryPart($joinClause);

        //then
        Assert::thatString($buildJoinQueryPart)->isEqualTo('LEFT JOIN products AS p ON p.id = order_products.product_id AND p.active = true');
    }

    /**
     * @test
     */
    public function shouldNotReplaceWhenTableNameIsPartOfOtherTableName()
    {
        //given
        $onClauses = array(new WhereClause('products.active = true'), new WhereClause('order_products.active = true'));
        $joinClause = new JoinClause('products', 'id', 'product_id', 'order_products', 'p', 'LEFT', $onClauses);

        //when
        $buildJoinQueryPart = DialectUtil::buildJoinQueryPart($joinClause);

        //then
        Assert::thatString($buildJoinQueryPart)
            ->isEqualTo('LEFT JOIN products AS p ON p.id = order_products.product_id AND p.active = true AND order_products.active = true');
    }
}
