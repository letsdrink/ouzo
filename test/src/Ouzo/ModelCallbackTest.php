<?php

namespace Ouzo;

use Ouzo\Tests\DbTransactionalTestCase;

class TestCallback
{
    public $args;

    function __invoke()
    {
        $this->args = func_get_args();
    }
}

class CallbackTestModel extends Model
{
    public function __construct($attributes = array(), $callback)
    {
        parent::__construct(array(
            'afterSave' => $callback,
            'table' => 'products',
            'primaryKey' => 'id',
            'attributes' => $attributes,
            'fields' => array('name')));
    }

    function _callback()
    {
        $this->callbackCalled = true;
    }
}

class ModelCallbackTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldCallAfterSaveCallbackOnInsert()
    {
        //given
        $callback = new TestCallback();
        $attributes = array('name' => 'bmw');
        $model = new CallbackTestModel($attributes, $callback);

        //when
        $model->insert();

        //then
        $this->assertEquals(array(array('name' => 'bmw', 'id' => $model->id)), $callback->args);
    }

    /**
     * @test
     */
    public function shouldCallAfterSaveCallbackOnUpdate()
    {
        //given
        $callback = new TestCallback();
        $attributes = array('name' => 'bmw');
        $model = new CallbackTestModel($attributes, $callback);
        $model->insert();

        //when
        $model->updateAttributes(array('name' => 'audi'));

        //then
        $this->assertEquals(array(array('name' => 'audi', 'id' => $model->id)), $callback->args);
    }

    /**
     * @test
     */
    public function shouldCallMemberFunctionCallback()
    {
        //given
        $model = new CallbackTestModel(array('name' => 'bmw'), '_callback');

        //when
        $model->insert();

        //then
        $this->assertTrue($model->callbackCalled);
    }
} 