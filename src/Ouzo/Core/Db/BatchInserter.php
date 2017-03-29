<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use ArrayIterator;
use Ouzo\Db;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use PDO;

class BatchInserter
{
    /**
     * @var Model[]
     */
    private $_models = [];

    public function add(Model $model)
    {
        $this->_models[] = $model;
    }

    public function execute()
    {
        if (empty($this->_models)) {
            return;
        }
        $this->_callBeforeSaveCallbacks();

        $metaInstance = Arrays::first($this->_models);
        $columns = $metaInstance->getFieldsWithoutPrimaryKey();
        $primaryKey = $metaInstance->getIdName();
        $table = $metaInstance->getTableName();

        $sql = DialectFactory::create()->batchInsert($table, $primaryKey, $columns, count($this->_models));
        $params = $this->_prepareParams($primaryKey);

        $ids = Arrays::flatten(Db::getInstance()->query($sql, $params)->fetchAll(PDO::FETCH_NUM));
        $this->_assignPrimaryKeys($primaryKey, $ids);
        $this->_callAfterSaveCallbacks();
    }

    private function _assignPrimaryKeys($primaryKey, $ids)
    {
        if ($primaryKey) {
            $primaryKeysIterator = new ArrayIterator($ids);
            foreach ($this->_models as $model) {
                $model->$primaryKey = $primaryKeysIterator->current();
                $primaryKeysIterator->next();
            }
        }
    }

    public function _callBeforeSaveCallbacks()
    {
        foreach ($this->_models as $model) {
            $model->_callBeforeSaveCallbacks();
        }
    }

    public function _callAfterSaveCallbacks()
    {
        foreach ($this->_models as $model) {
            $model->_callAfterSaveCallbacks();
            $model->_resetModifiedFields();
        }
    }

    public function _prepareParams($primaryKey)
    {
        $allValues = [];
        foreach ($this->_models as $model) {
            $attributes = $model->definedAttributes();
            unset($attributes[$primaryKey]);
            $values = array_values($attributes);
            $allValues = array_merge($allValues, $values);
        }
        return $allValues;
    }
}
