<?php
use Thulium\Model;
use Thulium\Tests\DbTransactionalTestCase;

class ModelTest extends DbTransactionalTestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenTableEmptyInPrepareParameters()
    {
        //when
        new Model(array('table' => ''));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenFieldsEmptInPrepareParameters()
    {
        //when
        new Model(array('table' => 't_example', 'fields' => ''));
    }

    /**
     * @test
     */
    public function shouldSetEmptyWhenPrimaryKeyEmpty()
    {
        //when
        $model = new Model(array('table' => 't_example', 'fields' => array('field1')));

        //then
        $this->assertSame('', $model->getIdName());
    }
}