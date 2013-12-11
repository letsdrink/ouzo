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
    /**
     * @var RelationToFetch[]
     */
    private $_relationsToFetch = array();
    private $_query;
    private $_selectModel = true;

    public function __construct(Model $model, $db = null, $alias = null)
    {
        $this->_db = $db ? $db : Db::getInstance();
        $this->_model = $model;

        $this->_query = new Query();
        $this->_query->table = $model->getTableName();
        $this->_query->aliasTable = $alias;
        $this->_query->selectColumns = array();
        $this->selectModelColumns($model, $this->getModelAliasOrTable());
    }

    private function getModelAliasOrTable()
    {
        return $this->_query->aliasTable ? : $this->_model->getTableName();
    }

    private function selectModelColumns(Model $metaInstance, $alias)
    {
        $prefix = $this->aliasPrefixForSelect($alias);
        $this->_query->selectColumns = $this->_query->selectColumns + ColumnAliasHandler::createSelectColumnsWithAliases($prefix, $metaInstance->_getFields(), $alias);
    }

    private function aliasPrefixForSelect($alias)
    {
        return "_{$alias}_";
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

    private function _fetchRelations($results)
    {
        foreach ($this->_relationsToFetch as $relationToFetch) {
            $relationFetcher = new RelationFetcher($relationToFetch->relation);
            $fieldTransformer = new FieldTransformer($relationToFetch->field, $relationFetcher);
            $fieldTransformer->transform($results);
        }
        return $results;
    }

    private function _processResults($results)
    {
        $models = array();
        foreach ($results as $row) {
            $models[] = $this->convertRowToModel($row);
        }
        return $this->_fetchRelations($models);
    }

    private function convertRowToModel($row)
    {
        $model = $this->_extractModelFromResult($this->_model, $this->getModelAliasOrTable(), $row);

        foreach ($this->_joinedModels as $joinedModel) {
            if ($joinedModel->storeField()) {
                $instance = $this->_extractModelFromResult($joinedModel->getModelObject(), $joinedModel->alias(), $row);
                Objects::setValueRecursively($model, $joinedModel->destinationField(), $instance);
            }
        }
        return $model;
    }

    private function _extractModelFromResult(Model $metaInstance, $alias, array $result)
    {
        $attributes = ColumnAliasHandler::extractAttributesForPrefix($result, $this->aliasPrefixForSelect($alias));
        if (Arrays::any($attributes, Functions::notEmpty())) {
            return $metaInstance->newInstance($attributes);
        }
        return null;
    }

    /**
     * Issues "delete from ... where ..." sql command.
     * Note that overridden Model::delete is not called.
     *
     */
    public function deleteAll()
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->delete();
    }

    /**
     * Calls Model::delete method for each matching object
     */
    public function deleteEach()
    {
        $objects = $this->fetchAll();
        return array_map(function ($object) {
            return !$object->delete();
        }, $objects);
    }

    /**
     * Runs an update query against a set of models
     */
    public function update(array $attributes)
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->update($attributes);
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param $type - join type, defaults to LEFT
     * @return ModelQueryBuilder
     */
    public function join($relationSelector, $aliases = null, $type = 'LEFT')
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->_model, $relationSelector);
        $relationWithAliases = ModelQueryBuilderHelper::associateRelationsWithAliases($relations, $aliases);
        $modelJoins = ModelQueryBuilderHelper::createModelJoins($this->getModelAliasOrTable(), $relationWithAliases, $type);
        foreach ($modelJoins as $modelJoin) {
            $this->addJoin($modelJoin);
        }
        return $this;
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @return ModelQueryBuilder
     */
    public function innerJoin($relationSelector, $aliases = null)
    {
        return $this->join($relationSelector, $aliases, 'INNER');
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @return ModelQueryBuilder
     */
    public function rightJoin($relationSelector, $aliases = null)
    {
        return $this->join($relationSelector, $aliases, 'RIGHT');
    }

    private function addJoin(ModelJoin $modelJoin)
    {
        if (!$this->isAlreadyJoined($modelJoin)) {
            $this->_query->addJoin($modelJoin->asJoinClause());
            $this->_joinedModels[] = $modelJoin;
            $this->selectModelColumns($modelJoin->getModelObject(), $modelJoin->alias());
        }
    }

    private function isAlreadyJoined(ModelJoin $modelJoin)
    {
        return Arrays::any($this->_joinedModels, ModelJoin::equalsPredicate($modelJoin));
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @return ModelQueryBuilder
     */
    public function with($relationSelector)
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->_model, $relationSelector);
        $field = '';

        foreach ($relations as $relation) {
            $this->_addRelationToFetch(new RelationToFetch($field, $relation));
            $field = $field ? $field . '->' . $relation->getName() : $relation->getName();
        }
        return $this;
    }

    private function _addRelationToFetch($relationToFetch)
    {
        if (!$this->isAlreadyAddedToFetch($relationToFetch)) {
            $this->_relationsToFetch[] = $relationToFetch;
        }
    }

    private function isAlreadyAddedToFetch(RelationToFetch $relationToFetch)
    {
        return Arrays::any($this->_relationsToFetch, RelationToFetch::equalsPredicate($relationToFetch));
    }

    /**
     * @return ModelQueryBuilder
     */
    public function select($columns, $type = PDO::FETCH_NUM)
    {
        $this->_selectModel = false;
        $this->_query->selectColumns = Arrays::toArray($columns);
        $this->_query->selectType = $type;
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