<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\ModelDefinition;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TestModel extends Model
{
    public function __construct(array $params)
    {
        parent::__construct($params);
    }
}

class ModelDefaultMappingTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ModelDefinition::resetCache();
    }

    #[Test]
    public function shouldCreateModelWithGivenMapping()
    {
        //when
        $testModel = new TestModel([
            'table' => 'table',
            'primaryKey' => 'key',
            'sequence' => 'sequence',
            'fields' => ['field1']
        ]);

        //then
        $this->assertEquals('key', $testModel->getIdName());
        $this->assertEquals('table', $testModel->getTableName());
        $this->assertEquals('sequence', $testModel->getSequenceName());
    }

    #[Test]
    public function shouldCreateModelWithDefaultMapping()
    {
        //when
        $testModel = new TestModel([
            'fields' => ['field1']
        ]);

        //then
        $this->assertEquals('id', $testModel->getIdName());
        $this->assertEquals('test_models', $testModel->getTableName());
        $this->assertEquals('test_models_id_seq', $testModel->getSequenceName());
    }

    #[Test]
    public function shouldCreateModelWithDefaultSequenceWhenPrimaryKeyGiven()
    {
        //when
        $testModel = new TestModel([
            'primaryKey' => 'key',
            'fields' => ['field1']
        ]);

        //then
        $this->assertEquals('key', $testModel->getIdName());
        $this->assertEquals('test_models_key_seq', $testModel->getSequenceName());
    }

    #[Test]
    public function shouldCreateModelWithDefaultSequenceWhenTableGiven()
    {
        //when
        $testModel = new TestModel([
            'table' => 'table',
            'fields' => ['field1']
        ]);

        //then
        $this->assertEquals('table_id_seq', $testModel->getSequenceName());
    }

    #[Test]
    public function shouldCreateModelWithEmptyPrimaryKeyAndSequence()
    {
        //when
        $testModel = new TestModel([
            'primaryKey' => '',
            'sequence' => '',
            'fields' => ['field1']
        ]);

        //then
        $this->assertEquals('', $testModel->getIdName());
        $this->assertEquals('', $testModel->getSequenceName());
    }
}
