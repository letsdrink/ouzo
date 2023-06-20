<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\OrderProduct;
use Ouzo\Db\ModelQueryBuilderHelper;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationWithAlias;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ModelQueryBuilderHelperTest extends TestCase
{
    #[Test]
    public function shouldExtractNestedRelations()
    {
        //given
        $root = OrderProduct::metaInstance();

        //when
        $relations = ModelQueryBuilderHelper::extractRelations($root, 'product->category');

        //then
        Assert::thatArray($relations)->containsExactly(
            new Relation('product', 'Test\Product', 'id_product', 'id', false),
            new Relation('category', 'Test\Category', 'id_category', 'id', false)
        );
    }

    #[Test]
    public function shouldExtractInlineRelation()
    {
        //given
        $root = OrderProduct::metaInstance();
        $inlineRelation = new Relation('orderProduct', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relations = ModelQueryBuilderHelper::extractRelations($root, $inlineRelation);

        //then
        Assert::thatArray($relations)->containsExactly(
            $inlineRelation
        );
    }

    #[Test]
    public function shouldAssociateRelationsWithAliasesIfFewerAliases()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases([$relation1, $relation2], 'r1');

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, 'r1'),
            new RelationWithAlias($relation2, null));
    }

    #[Test]
    public function shouldAssociateRelationsWithNullAliases()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases([$relation1, $relation2], null);

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, null),
            new RelationWithAlias($relation2, null));
    }

    #[Test]
    public function shouldAssociateRelationsWithAliasesByRelationNames()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases([
            $relation1, $relation2
        ], ['relation2' => 'r2']);

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, null),
            new RelationWithAlias($relation2, 'r2'));
    }
}
