<?php

use Ouzo\Db\PostgresDialect;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;

class PostgresDialectTest extends PHPUnit_Framework_TestCase
{

    private $dialect;

    protected  function setUp()
    {
        $this->dialect = new PostgresDialect();
    }

    /**
     * @test
     */
    public function shouldReturnSelectFrom()
    {
        // given
        $query = new Query();
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnDeleteFrom()
    {
        // given
        $query = new Query();
        $query->type = QueryType::$DELETE;
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('DELETE FROM products AS main', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectCountFrom()
    {
        // given
        $query = new Query();
        $query->type = QueryType::$COUNT;
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT count(*) FROM products AS main', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectColumnsFrom()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->selectColumns = array('id', 'name');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT id, name FROM products AS main', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithSingleOrderBy()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->order = 'id asc';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main ORDER BY id asc', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithMultipleOrderBy()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->order = array('id asc', 'name desc');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main ORDER BY id asc, name desc', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithLimit()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->limit = 10;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main LIMIT ?', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithOffset()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->offset = 10;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main OFFSET ?', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithSingleWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where('name = ?');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE name = ?', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithMultipleWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(array('name' => 'bob', 'id' => '1'));

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE name = ? AND id = ?', $sql);
    }

    /**
     * @test
     */
    public function shouldWrapConditionsWithOrInParenthesis()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(array('name' => 'bob', 'id' => '1'));
        $query->where('a = 1 or b = 2');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE name = ? AND id = ? AND (a = 1 or b = 2)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithWhereIn()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(array('name' => array('james', 'bob')));

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE name IN (?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithJoin()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->joinKey = 'id_category';
        $query->joinTable = 'categories';
        $query->idName = 'id_category';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main LEFT JOIN categories AS joined ON joined.id_category = main.id_category', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectForChainedWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(array('name' => 'james'));
        $query->where('id = ?', 1);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE name = ? AND id = ?', $sql);
    }

    /**
     * @test
     */
    public function shouldSkipEmptyWheres()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where();
        $query->where('id = ?', 1);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT main.* FROM products AS main WHERE id = ?', $sql);
    }
}