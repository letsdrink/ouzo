<?php
use Application\Model\Test\OrderProduct;
use Ouzo\Db\ModelQueryBuilderHelper;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationWithAlias;
use Ouzo\Tests\Assert;

class ModelQueryBuilderHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function shouldAssociateRelationsWithAliasesIfFewerAliases()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases(array($relation1, $relation2), 'r1');

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, 'r1'),
            new RelationWithAlias($relation2, null));
    }

    /**
     * @test
     */
    public function shouldAssociateRelationsWithNullAliases()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases(array($relation1, $relation2), null);

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, null),
            new RelationWithAlias($relation2, null));
    }

    /**
     * @test
     */
    public function shouldAssociateRelationsWithAliasesByRelationNames()
    {
        //given
        $relation1 = new Relation('relation1', 'Test\OrderProduct', 'id', 'id_product', false);
        $relation2 = new Relation('relation2', 'Test\OrderProduct', 'id', 'id_product', false);

        //when
        $relationToAlias = ModelQueryBuilderHelper::associateRelationsWithAliases(array($relation1, $relation2), array('relation2' => 'r2'));

        //then
        Assert::thatArray($relationToAlias)->containsExactly(
            new RelationWithAlias($relation1, null),
            new RelationWithAlias($relation2, 'r2'));
    }
}
