<?php
namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use PDO;

class ModelQueryBuilder
{
    /**
     * @var Db
     */
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
        $this->_query->selectType = PDO::FETCH_NUM;
        $this->_query->selectColumns = array();
        $this->selectModelColumns($model, $this->getModelAliasOrTable());
    }

    private function getModelAliasOrTable()
    {
        return $this->_query->aliasTable ?: $this->_model->getTableName();
    }

    private function selectModelColumns(Model $metaInstance, $alias)
    {
        if ($this->_selectModel) {
            $prefix = $this->aliasPrefixForSelect($alias);
            $this->_query->selectColumns = $this->_query->selectColumns + ColumnAliasHandler::createSelectColumnsWithAliases($prefix, $metaInstance->_getFields(), $alias);
        }
    }

    private function aliasPrefixForSelect($alias)
    {
        return "_{$alias}_";
    }

    /**
     * @param string $where
     * @param array $values
     * @return ModelQueryBuilder
     */
    public function where($where = '', $values = array())
    {
        $this->_query->whereClauses[] = $where instanceof WhereClause ? $where : new WhereClause($where, $values);
        return $this;
    }

    /**
     * @param $columns
     * @return ModelQueryBuilder
     */
    public function order($columns)
    {
        $this->_query->order = $columns;
        return $this;
    }

    /**
     * @param $offset
     * @return ModelQueryBuilder
     */
    public function offset($offset)
    {
        $this->_query->offset = $offset;
        return $this;
    }

    /**
     * @param $limit
     * @return ModelQueryBuilder
     */
    public function limit($limit)
    {
        $this->_query->limit = $limit;
        return $this;
    }

    public function count()
    {
        $this->_query->type = QueryType::$COUNT;
        $value = QueryExecutor::prepare($this->_db, $this->_query)->fetch();
        return intval(Arrays::firstOrNull(Arrays::toArray($value)));
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

    private function _fetchRelations($results, $joinsToStore)
    {
        $joinedRelations = Arrays::map($joinsToStore, Functions::extract()->destinationField());
        foreach ($this->_relationsToFetch as $relationToFetch) {
            if (!in_array($relationToFetch->destinationField, $joinedRelations)) {
                $relationFetcher = new RelationFetcher($relationToFetch->relation);
                $fieldTransformer = new FieldTransformer($relationToFetch->field, $relationFetcher);
                $fieldTransformer->transform($results);
            }
        }
        return $results;
    }

    private function _processResults($results)
    {
        $aliasToOffset = $this->_createAliasToOffsetMap();
        $joinsToStore = FluentArray::from($this->_joinedModels)
            ->filter(Functions::extract()->storeField())
            ->uniqueBy(Functions::extract()->destinationField())
            ->toArray();

        $models = array();
        foreach ($results as $row) {
            $models[] = $this->convertRowToModel($row, $aliasToOffset, $joinsToStore);
        }
        return $this->_fetchRelations($models, $joinsToStore);
    }

    private function _createAliasToOffsetMap()
    {
        $aliasToOffset = array();

        $aliasToOffset[$this->getModelAliasOrTable()] = 0;
        $offset = count($this->_model->getFields());
        foreach ($this->_joinedModels as $joinedModel) {
            if ($joinedModel->storeField()) {
                $aliasToOffset[$joinedModel->alias()] = $offset;
                $offset += count($joinedModel->getModelObject()->getFields());
            }
        }
        return $aliasToOffset;
    }

    private function convertRowToModel($row, $aliasToOffset, $joinsToStore)
    {
        $model = ModelQueryBuilderHelper::extractModelFromResult($this->_model, $row, $aliasToOffset[$this->getModelAliasOrTable()]);

        foreach ($joinsToStore as $joinedModel) {
            if ($joinedModel->storeField()) {
                $instance = ModelQueryBuilderHelper::extractModelFromResult($joinedModel->getModelObject(), $row, $aliasToOffset[$joinedModel->alias()]);
                Objects::setValueRecursively($model, $joinedModel->destinationField(), $instance);
            }
        }
        return $model;
    }

    /**
     * Issues "delete from ... where ..." sql command.
     * Note that overridden Model::delete is not called.
     *
     */
    public function deleteAll()
    {
        $this->_query->type = QueryType::$DELETE;
        return QueryExecutor::prepare($this->_db, $this->_query)->execute();
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
        $this->_query->type = QueryType::$UPDATE;
        $this->_query->updateAttributes = $attributes;
        return QueryExecutor::prepare($this->_db, $this->_query)->execute();
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param string $type - join type, defaults to LEFT
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function join($relationSelector, $aliases = null, $type = 'LEFT', $on = array())
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->_model, $relationSelector);
        $relationWithAliases = ModelQueryBuilderHelper::associateRelationsWithAliases($relations, $aliases);
        $modelJoins = ModelQueryBuilderHelper::createModelJoins($this->getModelAliasOrTable(), $relationWithAliases, $type, $on);
        foreach ($modelJoins as $modelJoin) {
            $this->addJoin($modelJoin);
        }
        return $this;
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function innerJoin($relationSelector, $aliases = null, $on = array())
    {
        return $this->join($relationSelector, $aliases, 'INNER', $on);
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function rightJoin($relationSelector, $aliases = null, $on = array())
    {
        return $this->join($relationSelector, $aliases, 'RIGHT', $on);
    }

    /**
     * @param $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function leftJoin($relationSelector, $aliases = null, $on = array())
    {
        return $this->join($relationSelector, $aliases, 'LEFT', $on);
    }

    private function addJoin(ModelJoin $modelJoin)
    {
        if (!$this->isAlreadyJoined($modelJoin)) {
            $this->_query->addJoin($modelJoin->asJoinClause());
            $this->_joinedModels[] = $modelJoin;
            if ($modelJoin->storeField()) {
                $this->selectModelColumns($modelJoin->getModelObject(), $modelJoin->alias());
            }
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
            $nestedField = $field ? $field . '->' . $relation->getName() : $relation->getName();
            $this->_addRelationToFetch(new RelationToFetch($field, $relation, $nestedField));
            $field = $nestedField;
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
     * @param $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public function select($columns, $type = PDO::FETCH_NUM)
    {
        $this->_selectModel = false;
        $this->_query->selectColumns = Arrays::toArray($columns);
        $this->_query->selectType = $type;
        return $this;
    }

    /**
     * @param $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public function selectDistinct($columns, $type = PDO::FETCH_NUM)
    {
        $this->_query->distinct = true;
        return $this->select($columns, $type);
    }

    public function __clone()
    {
        $this->_query = clone $this->_query;
    }

    public function copy()
    {
        return clone $this;
    }

    public function options(array $options)
    {
        $this->_query->options = $options;
        return $this;
    }

    public function groupBy($groupBy)
    {
        if ($this->_selectModel) {
            throw new DbException("Cannot use group by without specifying columns.\n"
                . "e.g. Model::select('column, count(*)')->groupBy('column')->fetchAll();");
        }

        $this->_query->groupBy = $groupBy;
        return $this;
    }
}
