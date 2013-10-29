<?php
namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use PDO;

class ModelQueryBuilder
{
    private $_db;
    private $_model;
    /**
     * @var ModelJoin[]
     */
    private $_joinedModels = array();
    private $_transformers;
    private $_query;
    private $_selectModel = true;

    public function __construct(Model $model, $db = null, $alias = null)
    {
        $this->_db = $db ? $db : Db::getInstance();
        $this->_model = $model;
        $this->_transformers = array();

        $this->_query = new Query();
        $this->_query->table = $model->getTableName();
        $this->_query->aliasTable = $alias;
        $this->_query->selectColumns = array();
        $this->selectModelColumns($model, $this->getModelAliasOrTable());
    }

    private function getModelAliasOrTable()
    {
        return $this->_query->aliasTable ?: $this->_model->getTableName();
    }

    private function selectModelColumns(Model $metaInstance, $alias)
    {
        $this->_query->selectColumns = $this->_query->selectColumns + ColumnAliasHandler::createSelectColumnsWithAliases("{$alias}_", $metaInstance->_getFields(), $alias);
    }

    /**
     * @return ModelQueryBuilder
     */
    public function where($where = '', $values = array())
    {
        $this->_query->whereClauses[] = new WhereClause($where, $values);
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function order($columns)
    {
        $this->_query->order = $columns;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function offset($offset)
    {
        $this->_query->offset = $offset;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function limit($limit)
    {
        $this->_query->limit = $limit;
        return $this;
    }

    public function count()
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->count();
    }

    /**
     * @return Model
     */
    public function fetch()
    {
        $result = QueryExecutor::prepare($this->_db, $this->_query)->fetch();
        if (!$result) {
            return null;
        }
        return !$this->_selectModel ? $result : Arrays::firstOrNull($this->_processResults(array($result)));
    }

    /**
     * @return Model[]
     */
    public function fetchAll()
    {
        $result = QueryExecutor::prepare($this->_db, $this->_query)->fetchAll();
        return !$this->_selectModel ? $result : $this->_processResults($result);
    }

    private function _transform($results)
    {
        foreach ($this->_transformers as $transformer) {
            $transformer->transform($results);
        }
        return $results;
    }

    private function _processResults($results)
    {
        $models = array();
        foreach ($results as $row) {
            $model = $this->extractModelFromResult($this->_model, $this->getModelAliasOrTable(), $row);
            $models[] = $model;

            foreach ($this->_joinedModels as $joinedModel) {
                if ($joinedModel->storeField()) {
                    $instance = $this->extractModelFromResult($joinedModel->getModelObject(), $joinedModel->alias(), $row);
                    Objects::setValueRecursively($model, $joinedModel->destinationField(), $instance);
                }
            }
        }
        return $this->_transform($models);
    }


    private function extractModelFromResult(Model $metaInstance, $alias, array $result)
    {
        $attributes = ColumnAliasHandler::extractAttributesForPrefix($result, "{$alias}_");
        if (Arrays::any($attributes, Functions::notEmpty())) {
            return $metaInstance->newInstance($attributes);
        }
        return null;
    }

    public function deleteAll()
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->delete();
    }

    public function deleteEach()
    {
        $objects = $this->fetchAll();
        return array_map(function ($object) {
            return !$object->delete();
        }, $objects);
    }

    /**
     * @return ModelQueryBuilder
     */
    public function join($relationName, $aliases = null)
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->_model, $relationName);
        $relationWithAliases = ModelQueryBuilderHelper::associateRelationsWithAliases($relations, $aliases);

        $field = '';
        $table = $this->getModelAliasOrTable();
        foreach ($relationWithAliases as $relationWithAlias) {
            $relation = $relationWithAlias->relation;
            $field = $field ? $field . '->' . $relation->getName() : $relation->getName();
            $modelJoin = new ModelJoin($field, $relation, $relationWithAlias->alias);
            $this->addJoin($table, $modelJoin);
            $table = $modelJoin->alias();
        }

        return $this;
    }

    private function addJoin($table, ModelJoin $modelJoin)
    {
        $this->_query->addJoin($modelJoin->asJoinClause($table));
        $this->_joinedModels[] = $modelJoin;
        $this->selectModelColumns($modelJoin->getModelObject(), $modelJoin->alias());
    }

    /**
     * @return ModelQueryBuilder
     */
    public function with($relationName)
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->_model, $relationName);
        $field = '';

        foreach ($relations as $relation) {
            $relationFetcher = new RelationFetcher($relation);
            $fieldTransformer = new FieldTransformer($field, $relationFetcher);

            $this->_transformers[] = $fieldTransformer;

            $field = $field ? $field . '->' . $relation->getName() : $relation->getName();
        }
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function select($columns)
    {
        $this->_selectModel = false;
        $this->_query->selectColumns = is_array($columns) ? $columns : array($columns);
        $this->_query->selectType = PDO::FETCH_NUM;
        return $this;
    }

    function __clone()
    {
        $this->_query = clone $this->_query;
    }

    function copy()
    {
        return clone $this;
    }
}