<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Options;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;

class SimpleModel extends Model
{
    public function __construct()
    {
        parent::__construct(['fields' => ['name']]);
    }
}

class StatementEmulatorTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldSubstituteParams()
    {
        //given
        $pdoStatement = Mock::mock();
        $pdo = Mock::mock();
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
