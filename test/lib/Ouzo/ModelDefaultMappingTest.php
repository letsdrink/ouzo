<?php
namespace Ouzo;

use Ouzo\Tests\DbTransactionalTestCase;

class TestModel extends Model
{
    function __construct(array $params)
    {
        parent::__construct($params);
    }
}

class ModelDefaultMappingTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldCreateModelWithGivenMapping()
    {
        //when
        $testModel = new TestModel(array(
            'table' => 'table',
            'primaryKey' => 'key',
            'sequence' => 'sequence',
            'fields' => array('field1')
        ));

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
        $testModel = new TestModel(array(
            'fields' => array('field1')
        ));

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
        $testModel = new TestModel(array(
            'primaryKey' => 'key',
            'fields' => array('field1')
        ));

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
        $testModel = new TestModel(array(
            'table' => 'table',
            'fields' => array('field1')
        ));

        //then
        $this->assertEquals('table_id_seq', $testModel->getSequenceName());
    }

    /**
     * @test
     */
    public function shouldCreateModelWithEmptyPrimaryKeyAndSequence()
    {
        //when
        $testModel = new TestModel(array(
            'primaryKey' => '',
            'sequence' => '',
            'fields' => array('field1')
        ));

        //then
        $this->assertEquals('', $testModel->getIdName());
        $this->assertEquals('', $testModel->getSequenceName());
    }
}