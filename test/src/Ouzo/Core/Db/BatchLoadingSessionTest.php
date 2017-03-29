<?php
use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Db\BatchLoadingSession;
use Ouzo\Db\Stats;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;


class BatchLoadingSessionTest extends DbTransactionalTestCase
{
    public function setUp()
    {
        parent::setUp();
        $_SESSION = [];
    }

    /**
     * @test
     */
    public function shouldLoadRelationsInAllModelsFromResultWhenAccessingAnyRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);

        Stats::reset();

        //when
        $products = BatchLoadingSession::run(function() {
            $models = Product::all();
            $models[0]->category;
            return $models;
        });

        //then
        Assert::thatArray($products)->extracting('category->id')->containsExactly($category->id, $category->id);
        $this->assertEquals(2, Stats::getNumberOfQueries());
    }

    /**
     * @test
     */
    public function shouldNotLoadRelationsEagerlyWhenSessionAllocated()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);

        Stats::reset();

        //when
        $products = BatchLoadingSession::run(function() {
            return Product::where()->with('category')->fetchAll();
        });

        //then
        Assert::thatArray($products)->hasSize(2);
        $this->assertEquals(1, Stats::getNumberOfQueries());
    }

    /**
     * @test
     */
    public function shouldDeallocateSession()
    {
        //when
        BatchLoadingSession::run(function() {
            return Product::where()->with('category')->fetchAll();
        });

        //then
        $this->assertFalse(BatchLoadingSession::isAllocated());
    }
}