<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Options;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\SimpleMock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SimpleModel extends Model
{
    public function __construct()
    {
        parent::__construct(['fields' => ['name']]);
    }
}

class StatementEmulatorTest extends DbTransactionalTestCase
{
    #[Test]
    public function shouldSubstituteParams()
    {
        //given
        $pdoStatement = Mock::mock(PDOStatement::class);
        $pdo = Mock::mock(PDO::class);
        /** @var Db|SimpleMock $db */
        $db = Mock::mock(Db::class);
        $db->dbHandle = $pdo;

        Mock::when($pdo)->query(Mock::anyArgList())->thenReturn($pdoStatement);
        Mock::when($pdo)->quote("bob")->thenReturn("'bob'");
        Mock::when($pdoStatement)->fetchAll(Mock::anyArgList())->thenReturn([]);

        $modelQueryBuilder = new ModelQueryBuilder(SimpleModel::metaInstance(), $db);

        //when
        $modelQueryBuilder->where(['name' => 'bob'])
            ->options([Options::EMULATE_PREPARES => true])
            ->fetchAll();

        //then
        Mock::verify($pdo)->query("SELECT simple_models.name, simple_models.id FROM simple_models WHERE name = 'bob' /* orm:model */");
    }
}
