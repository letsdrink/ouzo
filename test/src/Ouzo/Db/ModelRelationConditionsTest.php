<?php
use Model\Test\Category;
use Model\Test\Product;
use Ouzo\Model;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Arrays;

class ModelRelationConditionsTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithStringCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $products_starting_from_b = $category->products_starting_with_b;

        //then
        Assert::thatArray($products_starting_from_b)->hasSize(2)->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithStringCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $searchCategory = Category::where()->with('products_starting_with_b')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_starting_with_b'))
            ->hasSize(2)
            ->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithCallbackCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $products_ending_with_b_or_y = $category->products_ending_with_b_or_y;

        //then
        Assert::thatArray($products_ending_with_b_or_y)->hasSize(2)->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithCallbackCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $searchCategory = Category::where()->with('products_ending_with_b_or_y')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_ending_with_b_or_y'))
            ->hasSize(2)
            ->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithArrayCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $products_name_bob = $category->products_name_bob;

        //then
        Assert::thatArray($products_name_bob)->hasSize(1)->onProperty('name')->containsOnly('bob');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithArrayCondition()
    {
        //given
        $category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'bob', 'id_category' => $category->getId()));
        Product::create(array('name' => 'billy', 'id_category' => $category->getId()));
        Product::create(array('name' => 'peter', 'id_category' => $category->getId()));

        //when
        $searchCategory = Category::where()->with('products_name_bob')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_name_bob'))
            ->hasSize(1)
            ->onProperty('name')->containsOnly('bob');
    }

    static function getNoLazy(Model $model, $attribute)
    {
        return Arrays::getValue($model->attributes(), $attribute);
    }
}