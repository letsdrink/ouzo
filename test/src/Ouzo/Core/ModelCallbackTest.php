<?php
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;

class TestCallback
{
    public $args;

    public function __invoke()
    {
        $this->args = func_get_args();
    }
}

class CallbackTestModel extends Model
{
    public function __construct($attributes = array(), $beforeSaveCallback = null, $afterSaveCallback = null)
    {
        parent::__construct(array(
            'beforeSave' => $beforeSaveCallback,
            'afterSave' => $afterSaveCallback,
            'table' => 'products',
            'primaryKey' => 'id',
            'attributes' => $attributes,
            'fields' => array('name')));
    }

    public function _callback()
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
        $model = new CallbackTestModel($attributes, null, $callback);

        //when
        $model->insert();

        //then
        $this->assertEquals(array($model), $callback->args);
    }

    /**
     * @test
     */
    public function shouldCallAfterSaveCallbackOnUpdate()
    {
        //given
        $callback = new TestCallback();
        $attributes = array('name' => 'bmw');
        $model = new CallbackTestModel($attributes, null, $callback);
        $model->insert();

        //when
        $model->updateAttributes(array('name' => 'audi'));

        //then
        $this->assertEquals(array($model), $callback->args);
    }

    /**
     * @test
     */
    public function shouldCallBeforeSaveCallbackOnInsert()
    {
        //given
        $callback = function ($model) {
            $model->name = $model->name . '_updated';
        };
        $model = new CallbackTestModel(array('name' => 'bmw'), $callback, null);

        //when
        $model->insert();

        //then
        $this->assertEquals('bmw_updated', $model->reload()->name);
    }

    /**
     * @test
     */
    public function shouldCallBeforeSaveCallbackOnUpdate()
    {
        //given
        $callback = function ($model) {
            $model->name = $model->name . '_updated';
        };
        $model = new CallbackTestModel(array('name' => 'bmw'), $callback, null);
        $model->insert();

        //when
        $model->updateAttributes(array('name' => 'audi'));

        //then
        $this->assertEquals('audi_updated', $model->reload()->name);
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
