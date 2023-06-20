<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\ModelDefinition;
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
    public bool $callbackCalled = false;

    public function __construct($attributes = [], $beforeSaveCallback = null, $afterSaveCallback = null)
    {
        parent::__construct([
            'beforeSave' => $beforeSaveCallback,
            'afterSave' => $afterSaveCallback,
            'table' => 'products',
            'primaryKey' => 'id',
            'attributes' => $attributes,
            'fields' => ['name']
        ]);
    }

    public function _callback()
    {
        $this->callbackCalled = true;
    }
}

class ModelCallbackTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ModelDefinition::resetCache();
    }

    #[Test]
    public function shouldCallAfterSaveCallbackOnInsert()
    {
        //given
        $callback = new TestCallback();
        $attributes = ['name' => 'bmw'];
        $model = new CallbackTestModel($attributes, null, $callback);

        //when
        $model->insert();

        //then
        $this->assertEquals([$model], $callback->args);
    }

    #[Test]
    public function shouldCallAfterSaveCallbackOnUpdate()
    {
        //given
        $callback = new TestCallback();
        $attributes = ['name' => 'bmw'];
        $model = new CallbackTestModel($attributes, null, $callback);
        $model->insert();

        //when
        $model->updateAttributes(['name' => 'audi']);

        //then
        $this->assertEquals([$model], $callback->args);
    }

    #[Test]
    public function shouldCallBeforeSaveCallbackOnInsert()
    {
        //given
        $callback = function ($model) {
            $model->name = $model->name . '_updated';
        };
        $model = new CallbackTestModel(['name' => 'bmw'], $callback, null);

        //when
        $model->insert();

        //then
        $this->assertEquals('bmw_updated', $model->reload()->name);
    }

    #[Test]
    public function shouldCallBeforeSaveCallbackOnUpdate()
    {
        //given
        $callback = function ($model) {
            $model->name = $model->name . '_updated';
        };
        $model = new CallbackTestModel(['name' => 'bmw'], $callback, null);
        $model->insert();

        //when
        $model->updateAttributes(['name' => 'audi']);

        //then
        $this->assertEquals('audi_updated', $model->reload()->name);
    }

    #[Test]
    public function shouldCallMemberFunctionCallback()
    {
        //given
        $model = new CallbackTestModel(['name' => 'bmw'], '_callback');

        //when
        $model->insert();

        //then
        $this->assertTrue($model->callbackCalled);
    }
}
