<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
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
    private array $models = [];
    private ?OnConflict $onConflict = null;

    public function add(Model $model): void
    {
        $this->models[] = $model;
    }

    public function onConflict(OnConflict $onConflict): void
    {
        $this->onConflict = $onConflict;
    }

    public function execute(): void
    {
        if (empty($this->models)) {
            return;
        }
        $this->callBeforeSaveCallbacks();

        $metaInstance = Arrays::first($this->models);
        $columns = $metaInstance->getFieldsWithoutPrimaryKey();
        $primaryKey = $metaInstance->getIdName();
        $table = $metaInstance->getTableName();

        $sql = DialectFactory::create()->batchInsert($table, $primaryKey, $columns, count($this->models), $this->onConflict);
        $params = $this->prepareParams($primaryKey);

        $ids = Arrays::flatten(Db::getInstance()->query($sql, $params)->fetchAll(PDO::FETCH_NUM));
        $this->assignPrimaryKeys($primaryKey, $ids);
        $this->callAfterSaveCallbacks();
    }

    /** @param string[] $ids
     */
    private function assignPrimaryKeys(string $primaryKey, array $ids): void
    {
        if ($primaryKey) {
            $primaryKeysIterator = new ArrayIterator($ids);
            foreach ($this->models as $model) {
                $model->$primaryKey = $primaryKeysIterator->current();
                $primaryKeysIterator->next();
            }
        }
    }

    public function callBeforeSaveCallbacks(): void
    {
        foreach ($this->models as $model) {
            $model->callBeforeSaveCallbacks();
        }
    }

    public function callAfterSaveCallbacks(): void
    {
        foreach ($this->models as $model) {
            $model->callAfterSaveCallbacks();
            $model->resetModifiedFields();
        }
    }

    public function prepareParams(string $primaryKey): array
    {
        $allValues = [];
        foreach ($this->models as $model) {
            $attributes = $model->definedAttributes();
            unset($attributes[$primaryKey]);
            $values = array_values($attributes);
            $allValues = array_merge($allValues, $values);
        }
        if ($this->onConflict?->isUpdateAction()) {
            $allValues = array_merge($allValues, array_values($this->onConflict?->getOnConflictUpdateValues()));
        }
        return $allValues;
    }
}
