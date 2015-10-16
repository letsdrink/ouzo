<?php

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
        $primaryKeysIterator = new ArrayIterator($ids);
        foreach ($this->_models as $model) {
            $model->$primaryKey = $primaryKeysIterator->current();
            $primaryKeysIterator->next();
        }
    }

    function _callBeforeSaveCallbacks()
    {
        foreach ($this->_models as $model) {
            $model->_callBeforeSaveCallbacks();
        }
    }

    function _callAfterSaveCallbacks()
    {
        foreach ($this->_models as $model) {
            $model->_callAfterSaveCallbacks();
            $model->_resetUpdates();
        }
    }

    function _prepareParams($primaryKey)
    {
        $allValues = [];
        foreach ($this->_models as $model) {
            $attributes = $model->attributes();
            unset($attributes[$primaryKey]);
            $values = array_values($attributes);
            $allValues = array_merge($allValues, $values);
        }
        return $allValues;
    }
}