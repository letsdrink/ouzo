<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\ModelDefinition;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;

class TestModel extends Model
{
    public function __construct(array $params)
    {
        parent::__construct($params);
    }
}

class ModelDefaultMappingTest extends DbTransactionalTestCase
{
    public  function setUp()
    {
        parent::setUp();
        ModelDefinition::resetCache();
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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
