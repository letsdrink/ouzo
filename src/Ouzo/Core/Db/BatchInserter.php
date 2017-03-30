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
    /** @var Model[] */
    private $models = [];

    /**
     * @param Model $model
     */
    public function add(Model $model)
    {
        $this->models[] = $model;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (empty($this->models)) {
            return;
        }
        $this->callBeforeSaveCallbacks();

        $metaInstance = Arrays::first($this->models);
        $columns = $metaInstance->getFieldsWithoutPrimaryKey();
        $primaryKey = $metaInstance->getIdName();
        $table = $metaInstance->getTableName();

        $sql = DialectFactory::create()->batchInsert($table, $primaryKey, $columns, count($this->models));
        $params = $this->prepareParams($primaryKey);

        $ids = Arrays::flatten(Db::getInstance()->query($sql, $params)->fetchAll(PDO::FETCH_NUM));
        $this->assignPrimaryKeys($primaryKey, $ids);
        $this->callAfterSaveCallbacks();
    }

    /**
     * @param string $primaryKey
     * @param array $ids
     * @return void
     */
    private function assignPrimaryKeys($primaryKey, $ids)
    {
        if ($primaryKey) {
            $primaryKeysIterator = new ArrayIterator($ids);
            foreach ($this->models as $model) {
                $model->$primaryKey = $primaryKeysIterator->current();
                $primaryKeysIterator->next();
            }
        }
    }

    /**
     * @return void
     */
    public function callBeforeSaveCallbacks()
    {
        foreach ($this->models as $model) {
            $model->callBeforeSaveCallbacks();
        }
    }

    /**
     * @return void
     */
    public function callAfterSaveCallbacks()
    {
        foreach ($this->models as $model) {
            $model->callAfterSaveCallbacks();
            $model->resetModifiedFields();
        }
    }

    /**
     * @param string $primaryKey
     * @return array
     */
    public function prepareParams($primaryKey)
    {
        $allValues = [];
        foreach ($this->models as $model) {
            $attributes = $model->definedAttributes();
            unset($attributes[$primaryKey]);
            $values = array_values($attributes);
            $allValues = array_merge($allValues, $values);
        }
        return $allValues;
    }
}
