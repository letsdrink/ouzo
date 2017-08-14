<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Iterator;
use Ouzo\Db;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Iterator\BatchingIterator;
use Ouzo\Utilities\Iterator\TransformingIterator;
use Ouzo\Utilities\Iterator\UnbatchingIterator;
use PDO;

class ModelQueryBuilder
{
    const MODEL_QUERY_MARKER_COMMENT = 'orm:model';

    /** @var Db */
    private $db;
    /** @var Model */
    private $model;
    /** @var ModelJoin[] */
    private $joinedModels = [];
    /** @var RelationToFetch[] */
    private $relationsToFetch = [];
    /** @var Query */
    private $query;
    /** @var bool */
    private $selectModel = true;

    /**
     * @param Model $model
     * @param Db|null $db
     * @param string|null $alias
     */
    public function __construct(Model $model, Db $db = null, $alias = null)
    {
        $this->db = $db ? $db : Db::getInstance();
        $this->model = $model;

        $this->query = new Query();
        $this->query->table = $model->getTableName();
        $this->query->aliasTable = $alias;
        $this->query->selectType = PDO::FETCH_NUM;
        $this->query->selectColumns = [];
        $this->selectModelColumns($model, $this->getModelAliasOrTable());
    }

    /**
     * @return string
     */
    private function getModelAliasOrTable()
    {
        return $this->query->aliasTable ?: $this->model->getTableName();
    }

    /**
     * @param Model $metaInstance
     * @param string $alias
     * @return void
     */
    private function selectModelColumns(Model $metaInstance, $alias)
    {
        if ($this->selectModel) {
            $this->query->selectColumns = array_merge($this->query->selectColumns, ColumnAliasHandler::createSelectColumnsWithAliases($metaInstance->_getFields(), $alias));
        }
    }

    /**
     * @param string $where
     * @param array $values
     * @return ModelQueryBuilder
     */
    public function where($where = '', $values = [])
    {
        $this->query->where($where, $values);
        return $this;
    }

    /**
     * @param string $columns
     * @return ModelQueryBuilder
     */
    public function order($columns)
    {
        $this->query->order = $columns;
        return $this;
    }

    /**
     * @param int $offset
     * @return ModelQueryBuilder
     */
    public function offset($offset)
    {
        $this->query->offset = $offset;
        return $this;
    }

    /**
     * @param int $limit
     * @return ModelQueryBuilder
     */
    public function limit($limit)
    {
        $this->query->limit = $limit;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function lockForUpdate()
    {
        $this->query->lockForUpdate = true;
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->query->type = QueryType::$COUNT;
        $value = (array)QueryExecutor::prepare($this->db, $this->query)->fetch();
        return intval(Arrays::firstOrNull(Arrays::toArray($value)));
    }


    /**
     * @return void
     */
    private function beforeSelect()
    {
        if ($this->selectModel) {
            $this->query->comment(ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT);
        }
    }

    /**
     * @return Model
     */
    public function fetch()
    {
        $this->beforeSelect();
        $result = QueryExecutor::prepare($this->db, $this->query)->fetch();
        if (!$result) {
            return null;
        }
        return !$this->selectModel ? $result : Arrays::firstOrNull($this->_processResults([$result]));
    }

    /**
     * @return Model[]
     */
    public function fetchAll()
    {
        $this->beforeSelect();
        $result = QueryExecutor::prepare($this->db, $this->query)->fetchAll();
        return !$this->selectModel ? $result : $this->_processResults($result);
    }

    /**
     * @param int $batchSize
     * @return Iterator
     */
    public function fetchIterator($batchSize = 500)
    {
        $this->beforeSelect();
        $iterator = QueryExecutor::prepare($this->db, $this->query)->fetchIterator();
        $iterator->rewind();
        return !$this->selectModel ? $iterator : new UnbatchingIterator(new TransformingIterator(new BatchingIterator($iterator, $batchSize), [$this, '_processResults']));
    }

    /**
     * @param array $results
     * @return Model[]
     */
    public function _processResults($results)
    {
        $resultSetConverter = new ModelResultSetConverter($this->model, $this->getModelAliasOrTable(), $this->joinedModels, $this->relationsToFetch);
        $converted = $resultSetConverter->convert($results);
        BatchLoadingSession::attach($converted);
        return $converted;
    }

    /**
     * Issues "delete from ... where ..." sql command.
     * Note that overridden Model::delete is not called.
     * @return int
     */
    public function deleteAll()
    {
        $this->query->type = QueryType::$DELETE;
        return QueryExecutor::prepare($this->db, $this->query)->execute();
    }

    /**
     * Calls Model::delete method for each matching object
     * @return array
     */
    public function deleteEach()
    {
        $objectIterator = $this->fetchIterator();
        $result = [];
        foreach ($objectIterator as $object) {
            $result[] = !$object->delete();
        }
        return $result;
    }

    /**
     * Runs an update query against a set of models
     * @param array $attributes
     * @return int
     */
    public function update(array $attributes)
    {
        $this->query->type = QueryType::$UPDATE;
        $this->query->updateAttributes = $attributes;
        return QueryExecutor::prepare($this->db, $this->query)->execute();
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param string|null|array $aliases - alias of the first joined table or array of aliases for nested joins
     * @param string $type - join type, defaults to LEFT
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function join($relationSelector, $aliases = null, $type = 'LEFT', $on = [])
    {
        $modelJoins = $this->createModelJoins($relationSelector, $aliases, $type, $on);
        foreach ($modelJoins as $modelJoin) {
            $this->addJoin($modelJoin);
        }
        return $this;
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param string|null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function innerJoin($relationSelector, $aliases = null, $on = [])
    {
        return $this->join($relationSelector, $aliases, 'INNER', $on);
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param string|null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function rightJoin($relationSelector, $aliases = null, $on = [])
    {
        return $this->join($relationSelector, $aliases, 'RIGHT', $on);
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param string|null $aliases - alias of the first joined table or array of aliases for nested joins
     * @param array $on
     * @return ModelQueryBuilder
     */
    public function leftJoin($relationSelector, $aliases = null, $on = [])
    {
        return $this->join($relationSelector, $aliases, 'LEFT', $on);
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @param string|null|array $aliases - alias of the first joined table or array of aliases for nested joins
     * @return ModelQueryBuilder
     */
    public function using($relationSelector, $aliases)
    {
        $modelJoins = $this->createModelJoins($relationSelector, $aliases, 'USING', []);
        foreach ($modelJoins as $modelJoin) {
            $this->query->addUsing($modelJoin->asJoinClause());
        }
        return $this;
    }

    /**
     * @param ModelJoin $modelJoin
     * @return void
     */
    private function addJoin(ModelJoin $modelJoin)
    {
        if (!$this->isAlreadyJoined($modelJoin)) {
            $this->query->addJoin($modelJoin->asJoinClause());
            $this->joinedModels[] = $modelJoin;
            if ($modelJoin->storeField()) {
                $this->selectModelColumns($modelJoin->getModelObject(), $modelJoin->alias());
            }
        }
    }

    /**
     * @param ModelJoin $modelJoin
     * @return bool
     */
    private function isAlreadyJoined(ModelJoin $modelJoin)
    {
        return Arrays::any($this->joinedModels, ModelJoin::equalsPredicate($modelJoin));
    }

    /**
     * @param string $relationSelector - Relation object, relation name or nested relations 'rel1->rel2'
     * @return ModelQueryBuilder
     */
    public function with($relationSelector)
    {
        if (!BatchLoadingSession::isAllocated()) {
            $relations = ModelQueryBuilderHelper::extractRelations($this->model, $relationSelector);
            $field = '';

            foreach ($relations as $relation) {
                $nestedField = $field ? $field . '->' . $relation->getName() : $relation->getName();
                $this->addRelationToFetch(new RelationToFetch($field, $relation, $nestedField));
                $field = $nestedField;
            }
        }
        return $this;
    }

    /**
     * @param string $relationToFetch
     */
    private function addRelationToFetch($relationToFetch)
    {
        if (!$this->isAlreadyAddedToFetch($relationToFetch)) {
            $this->relationsToFetch[] = $relationToFetch;
        }
    }

    /**
     * @param RelationToFetch $relationToFetch
     * @return bool
     */
    private function isAlreadyAddedToFetch(RelationToFetch $relationToFetch)
    {
        return Arrays::any($this->relationsToFetch, RelationToFetch::equalsPredicate($relationToFetch));
    }

    /**
     * @param array|string $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public function select($columns, $type = PDO::FETCH_NUM)
    {
        $this->selectModel = false;
        $this->query->selectColumns = Arrays::toArray($columns);
        $this->query->selectType = $type;
        return $this;
    }

    /**
     * @param string|array $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public function selectDistinct($columns, $type = PDO::FETCH_NUM)
    {
        $this->query->distinct = true;
        return $this->select($columns, $type);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->query->options = $options;
        return $this;
    }

    /**
     * @param string $groupBy
     * @throws DbException
     * @return $this
     */
    public function groupBy($groupBy)
    {
        if ($this->selectModel) {
            throw new DbException("Cannot use group by without specifying columns.\n"
                . "e.g. Model::select('column, count(*)')->groupBy('column')->fetchAll();");
        }

        $this->query->groupBy = $groupBy;
        return $this;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $relationSelector
     * @param string|null|array $aliases
     * @param string $type
     * @param array $on
     * @return ModelJoin[]
     */
    private function createModelJoins($relationSelector, $aliases, $type, $on)
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->model, $relationSelector, $this->joinedModels);
        $relationWithAliases = ModelQueryBuilderHelper::associateRelationsWithAliases($relations, $aliases);
        return ModelQueryBuilderHelper::createModelJoins($this->getModelAliasOrTable(), $relationWithAliases, $type, $on);
    }
}
