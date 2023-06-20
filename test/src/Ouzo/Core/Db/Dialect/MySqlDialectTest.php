<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Product;
use Ouzo\Config;
use Ouzo\Db\BatchInserter;
use Ouzo\Db\Dialect\MySqlDialect;
use Ouzo\Db\JoinClause;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MySqlDialectTest extends TestCase
{
    private MySqlDialect $dialect;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dialect = new MySqlDialect();
    }

    #[Test]
    public function shouldReturnSelectFrom()
    {
        // given
        $query = new Query();
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectFromWithAlias()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->aliasTable = 'p';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products AS p', $sql);
    }

    #[Test]
    public function shouldReturnDeleteFrom()
    {
        // given
        $query = new Query();
        $query->type = QueryType::$DELETE;
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('DELETE FROM products', $sql);
    }

    #[Test]
    public function shouldReturnDeleteFromWithJoin()
    {
        // given
        $query = new Query();
        $query->type = QueryType::$DELETE;
        $query->table = 'products';
        $query->addUsing(new JoinClause('categories', 'id_category', 'id', 'products', 'c', 'USING', []));

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('DELETE FROM products USING products INNER JOIN categories AS c WHERE (c.id_category = products.id)', $sql);
    }

    #[Test]
    public function shouldReturnSelectCountFrom()
    {
        // given
        $query = new Query();
        $query->type = QueryType::$COUNT;
        $query->table = 'products';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT count(*) FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectColumnsFrom()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->selectColumns = ['id', 'name'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT id, name FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithSingleOrderBy()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->order = 'id asc';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id asc', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithMultipleOrderBy()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->order = ['id asc', 'name desc'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id asc, name desc', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithLimit()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->limit = 10;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products LIMIT ?', $sql);
    }

    #[Test]
    public function shouldReturnGroupBy()
    {
        // given
        $query = Query::select(['category', 'count(*)'])
            ->table('products')
            ->groupBy('category');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT category, count(*) FROM products GROUP BY category', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithOffset()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->offset = 10;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products OFFSET ?', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithSingleWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where('name = ?');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ?', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithMultipleWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(['name' => 'bob', 'id' => '1']);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ? AND id = ?', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithWhereIn()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(['name' => ['james', 'bob']]);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name IN (?, ?)', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithJoin()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->join('categories', 'id_category', 'id_category');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products LEFT JOIN categories ON categories.id_category = products.id_category', $sql);
    }

    #[Test]
    public function shouldReturnSelectForChainedWhere()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(['name' => 'james']);
        $query->where('id = ?', 1);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ? AND id = ?', $sql);
    }

    #[Test]
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
        $this->assertEquals('SELECT * FROM products WHERE id = ?', $sql);
    }

    #[Test]
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
        $sql = $this->dialect->buildQuery($query);

        //then
        $expected = 'SELECT * FROM products LEFT JOIN categories ON categories.id_category = products.id_category LEFT JOIN orders ON orders.id = products.id_product WHERE id = ?';
        $this->assertEquals($expected, $sql);
    }

    #[Test]
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
        $sql = $this->dialect->buildQuery($query);

        //then
        $expected = 'SELECT * FROM products LEFT JOIN categories AS c ON c.id_category = products.id_category LEFT JOIN orders AS o ON o.id = products.id_product WHERE id = ?';
        $this->assertEquals($expected, $sql);
    }

    #[Test]
    public function shouldReturnSelectForUpdate()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->lockForUpdate = true;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products FOR UPDATE', $sql);
    }

    #[Test]
    public function shouldThrowOnBatchInsert()
    {
        //given
        $previous = Config::getValue('sql_dialect');
        Config::overrideProperty('sql_dialect')->with(MySqlDialect::class);
        $inserter = new BatchInserter();
        $inserter->add(new Product(['name' => 'product1']));

        //when
        CatchException::when($inserter)->execute();

        //then
        CatchException::assertThat()
            ->hasMessage("Batch insert not supported in mysql")
            ->isInstanceOf('InvalidArgumentException');
        Config::overrideProperty('sql_dialect')->with($previous);
    }

    #[Test]
    public function shouldBuildUpsertQuery()
    {
        //given
        $query = new Query();
        $query->table = 'products';
        $query->type = QueryType::$UPSERT;
        $query->updateAttributes = ['col1' => 'val1', 'col2' => 'val2'];

        //when
        $sql = $this->dialect->buildQuery($query);

        //then
        $this->assertEquals("INSERT INTO products (col1, col2) VALUES (?, ?) ON DUPLICATE KEY UPDATE col1 = ?, col2 = ?", $sql);
    }
}
