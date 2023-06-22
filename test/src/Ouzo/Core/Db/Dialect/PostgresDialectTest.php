<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\Any;
use Ouzo\Db\Dialect\PostgresDialect;
use Ouzo\Db\JoinClause;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PostgresDialectTest extends TestCase
{
    private PostgresDialect $dialect;

    protected function setUp(): void
    {
        $this->dialect = new PostgresDialect();
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
        $this->assertEquals('DELETE FROM products USING categories AS c WHERE (c.id_category = products.id)', $sql);
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
    public function shouldIgnoreOrderLimitAndOffsetForSelectCount()
    {
        // given
        $query = Query::count()
            ->table('products')
            ->order('name desc')
            ->limit(1)
            ->offset(10);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT count(*) FROM products', $sql);
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
        $query->order = 'id ASC';

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id ASC', $sql);
    }

    #[Test]
    public function shouldReturnSelectWithMultipleOrderBy()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->order = ['id ASC', 'name DESC'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products ORDER BY id ASC, name DESC', $sql);
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
    public function shouldWrapConditionsWithOrInParenthesis()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(['name' => 'bob', 'id' => '1']);
        $query->where('a = 1 OR b = 2');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE name = ? AND id = ? AND (a = 1 OR b = 2)', $sql);
    }

    #[Test]
    public function shouldUseOrOperator()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(Any::of(['name' => 'bob', 'id' => '1']));

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE (name = ? OR id = ?)', $sql);
    }

    #[Test]
    public function shouldUseBothAndOrOperator()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->where(['a' => '5']);
        $query->where(Any::of(['name' => 'bob', 'id' => '1']));

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products WHERE a = ? AND (name = ? OR id = ?)', $sql);
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
    public function shouldReturnSelectWithJoinWithOnClause()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->join('categories', 'id_category', 'id_category', null, 'LEFT', ['col' => 2]);

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products LEFT JOIN categories ON categories.id_category = products.id_category AND col = ?', $sql);
    }

    #[Test]
    public function shouldAddAliases()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->aliasTable = 'p';
        $query->join('categories', 'id_category', 'id_category', 'c');

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT * FROM products AS p LEFT JOIN categories AS c ON c.id_category = p.id_category', $sql);
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
    public function shouldBuildUpdateQuery()
    {
        //given
        $query = new Query();
        $query->table = 'products';
        $query->type = QueryType::$UPDATE;
        $query->updateAttributes = ['col1' => 'val1', 'col2' => 'val2'];
        $query->where(['col1' => 'prev1', 'col2' => 'prev2']);

        //when
        $sql = $this->dialect->buildQuery($query);

        //then
        $this->assertEquals("UPDATE products SET col1 = ?, col2 = ? WHERE col1 = ? AND col2 = ?", $sql);
    }

    #[Test]
    public function shouldBuildUpsertQuery()
    {
        //given
        $query = new Query();
        $query->table = 'products';
        $query->type = QueryType::$UPSERT;
        $query->updateAttributes = ['col1' => 'val1', 'col2' => 'val2'];
        $query->upsertConflictColumns = ['col3', 'col4'];

        //when
        $sql = $this->dialect->buildQuery($query);

        //then
        $this->assertEquals("INSERT INTO products (col1, col2) VALUES (?, ?) ON CONFLICT (col3, col4) DO UPDATE SET col1 = ?, col2 = ?", $sql);
    }

    #[Test]
    public function shouldReturnSelectDistinctFrom()
    {
        // given
        $query = new Query();
        $query->selectColumns = ['name'];
        $query->table = 'products';
        $query->distinct = true;

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT DISTINCT name FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectDistinctOn()
    {
        // given
        $query = new Query();
        $query->selectColumns = ['name', 'category'];
        $query->table = 'products';
        $query->distinctOnColumns = ['description'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT DISTINCT ON (description) name, category FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectDistinctOnWithSelectingAllColumns()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->distinctOnColumns = ['description'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT DISTINCT ON (description) * FROM products', $sql);
    }

    #[Test]
    public function shouldReturnSelectDistinctOnWithCount()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->type = QueryType::$COUNT;
        $query->distinctOnColumns = ['description'];

        // when
        $sql = $this->dialect->buildQuery($query);

        // then
        $this->assertEquals('SELECT count(*) FROM (SELECT DISTINCT ON (description) * FROM products) AS products', $sql);
    }

    #[Test]
    public function selectDistinctWithCountShouldNotBeSupported()
    {
        // given
        $query = new Query();
        $query->table = 'products';
        $query->type = QueryType::$COUNT;
        $query->distinct = true;

        // when
        CatchException::when($this->dialect)->buildQuery($query);

        // then
        CatchException::assertThat()->isInstanceOf(DbException::class);
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
}
