<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\Dialect\DialectUtil;
use Ouzo\Db\JoinClause;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DialectUtilTest extends TestCase
{
    #[Test]
    public function shouldReplaceTableWithAlias()
    {
        //given
        $joinClause = new JoinClause('products', 'id', 'product_id', 'order_products', 'p', 'LEFT', [WhereClause::create('products.active = true')]);

        //when
        $buildJoinQueryPart = DialectUtil::buildJoinQueryPart($joinClause);

        //then
        Assert::thatString($buildJoinQueryPart)
            ->isEqualTo('LEFT JOIN products AS p ON p.id = order_products.product_id AND p.active = true');
    }

    #[Test]
    public function shouldNotReplaceWhenTableNameIsPartOfOtherTableName()
    {
        //given
        $onClauses = [
            WhereClause::create('products.active = true'), WhereClause::create('order_products.active = true')
        ];
        $joinClause = new JoinClause('products', 'id', 'product_id', 'order_products', 'p', 'LEFT', $onClauses);

        //when
        $buildJoinQueryPart = DialectUtil::buildJoinQueryPart($joinClause);

        //then
        Assert::thatString($buildJoinQueryPart)
            ->isEqualTo('LEFT JOIN products AS p ON p.id = order_products.product_id AND p.active = true AND order_products.active = true');
    }
}
