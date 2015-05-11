<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\Dialect\MySqlDialect;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;

class MySqlDialectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MySqlDialect
     */
    private $_dialect;

    protected function setUp()
    {
        parent::setUp();
        $this->_dialect = new MySqlDialect();
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('DELETE FROM products', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT count(*) FROM products', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT id, name FROM products', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id asc', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id asc, name desc', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products LIMIT ?', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnGroupBy()
    {
        // given
        $query = Query::select(array('category', 'count(*)'))
            ->table('products')
            ->groupBy('category');

        // when
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT category, count(*) FROM products GROUP BY category', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products OFFSET ?', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ?', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ? AND id = ?', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name IN (?, ?)', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithJoin()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->join('categories', 'id_category', 'id_category');

        // when
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products LEFT JOIN categories ON categories.id_category = products.id_category', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ? AND id = ?', $sql);
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
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE id = ?', $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithMultipleJoins()
    {
        //given
        $query = new Query();
        $query->table = 'products';
        $query
            ->join('categories', 'id_category', 'id_category')
            ->join('orders', 'id', 'id_product')
            ->where('id = ?', 1);

        //when
        $sql = $this->_dialect->buildQuery($query);

        //then
        $expected = 'SELECT * FROM products LEFT JOIN categories ON categories.id_category = products.id_category LEFT JOIN orders ON orders.id = products.id_product WHERE id = ?';
        $this->assertEquals($expected, $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWithMultipleJoinsWithAliases()
    {
        //given
        $query = new Query();
        $query->table = 'products';
        $query
            ->join('categories', 'id_category', 'id_category', 'c')
            ->join('orders', 'id', 'id_product', 'o')
            ->where('id = ?', 1);

        //when
        $sql = $this->_dialect->buildQuery($query);

        //then
        $expected = 'SELECT * FROM products LEFT JOIN categories AS c ON c.id_category = products.id_category LEFT JOIN orders AS o ON o.id = products.id_product WHERE id = ?';
        $this->assertEquals($expected, $sql);
    }

    /**
     * @test
     */
    public function shouldReturnSelectForUpdate()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->lockForUpdate = true;

        // when
        $sql = $this->_dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products FOR UPDATE', $sql);
    }
}
