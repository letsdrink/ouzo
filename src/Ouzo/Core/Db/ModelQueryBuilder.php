<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Iterator;
use Ouzo\Db;
use Ouzo\Db\WhereClause\WhereClause;
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

    private Db $db;
    /** @var ModelJoin[] */
    private array $joinedModels = [];
    /** @var RelationToFetch[] */
    private array $relationsToFetch = [];
    private Query $query;
    private bool $selectModel = true;

    public function __construct(private Model $model, ?Db $db = null, ?string $alias = null)
    {
        $this->db = $db ?: Db::getInstance();

        $this->query = new Query();
        $this->query->table = $model->getTableName();
        $this->query->aliasTable = $alias;
        $this->query->selectType = PDO::FETCH_NUM;
        $this->query->selectColumns = [];
        $this->selectModelColumns($model, $this->getModelAliasOrTable());
    }

    private function getModelAliasOrTable(): string
    {
        return $this->query->aliasTable ?: $this->model->getTableName();
    }

    private function selectModelColumns(Model $metaInstance, string $alias): void
    {
        if ($this->selectModel) {
            $this->query->selectColumns = array_merge($this->query->selectColumns, ColumnAliasHandler::createSelectColumnsWithAliases($metaInstance->_getFields(), $alias));
        }
    }

    public function where(string|array|WhereClause $where = '', mixed $values = []): static
    {
        $this->query->where($where, $values);
        return $this;
    }

    public function order(array|string|null $columns): static
    {
        $this->query->order = $columns;
        return $this;
    }

    public function offset(?int $offset): static
    {
        $this->query->offset = $offset;
        return $this;
    }

    public function limit(?int $limit): static
    {
        $this->query->limit = $limit;
        return $this;
    }

    public function lockForUpdate(): static
    {
        $this->query->lockForUpdate = true;
        return $this;
    }

    public function count(): int
    {
        $this->query->type = QueryType::$COUNT;
        $value = (array)QueryExecutor::prepare($this->db, $this->query)->fetch();
        return intval(Arrays::firstOrNull(Arrays::toArray($value)));
    }

    private function beforeSelect(): void
    {
        if ($this->selectModel) {
            $this->query->comment(ModelQueryBuilder::MODEL_QUERY_MARKER_COMMENT);
        }
    }

    public function fetch(): Model|array|null
    {
        $this->beforeSelect();
        $result = QueryExecutor::prepare($this->db, $this->query)->fetch();
        if (!$result) {
            return null;
        }
        return !$this->selectModel ? $result : Arrays::firstOrNull($this->processResults([$result]));
    }

    /** @return Model[] */
    public function fetchAll(): array
    {
        $this->beforeSelect();
        $result = QueryExecutor::prepare($this->db, $this->query)->fetchAll();
        return !$this->selectModel ? $result : $this->processResults($result);
    }

    public function fetchIterator(int $batchSize = 500): Iterator
    {
        $this->beforeSelect();
        $iterator = QueryExecutor::prepare($this->db, $this->query)->fetchIterator();
        $iterator->rewind();
        return !$this->selectModel ? $iterator : new UnbatchingIterator(new TransformingIterator(new BatchingIterator($iterator, $batchSize), fn($result) => $this->processResults($result)));
    }

    /** @return Model[] */
    public function processResults(array $results): array
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
    public function deleteAll(): int
    {
        $this->query->type = QueryType::$DELETE;
        return QueryExecutor::prepare($this->db, $this->query)->execute();
    }

    /**
     * Calls Model::delete method for each matching object
     * @return bool[]
     */
    public function deleteEach(): array
    {
        $objectIterator = $this->fetchIterator();
        $result = [];
        /** @var Model $object */
        foreach ($objectIterator as $object) {
            $result[] = !$object->delete();
        }
        return $result;
    }

    /**
     * Runs an update query against a set of models
     */
    public function update(array $attributes): int
    {
        $this->query->type = QueryType::$UPDATE;
        $this->query->updateAttributes = $attributes;
        return QueryExecutor::prepare($this->db, $this->query)->execute();
    }

    public function join(Relation|string $relationSelector, array|string|null $aliases = null, string $type = 'LEFT', array|string $on = []): static
    {
        $modelJoins = $this->createModelJoins($relationSelector, $aliases, $type, $on);
        foreach ($modelJoins as $modelJoin) {
            $this->addJoin($modelJoin);
        }
        return $this;
    }

    public function innerJoin(Relation|string $relationSelector, array|string|null $aliases = null, array|string $on = []): static
    {
        return $this->join($relationSelector, $aliases, 'INNER', $on);
    }

    public function rightJoin(Relation|string $relationSelector, array|string|null $aliases = null, array|string $on = []): static
    {
        return $this->join($relationSelector, $aliases, 'RIGHT', $on);
    }

    public function leftJoin(Relation|string $relationSelector, array|string|null $aliases = null, array|string $on = []): static
    {
        return $this->join($relationSelector, $aliases, 'LEFT', $on);
    }

    public function using(Relation|string $relationSelector, array|string|null $aliases): static
    {
        $modelJoins = $this->createModelJoins($relationSelector, $aliases, 'USING', []);
        foreach ($modelJoins as $modelJoin) {
            $this->query->addUsing($modelJoin->asJoinClause());
        }
        return $this;
    }

    private function addJoin(ModelJoin $modelJoin): void
    {
        if (!$this->isAlreadyJoined($modelJoin)) {
            $this->query->addJoin($modelJoin->asJoinClause());
            $this->joinedModels[] = $modelJoin;
            if ($modelJoin->storeField()) {
                $this->selectModelColumns($modelJoin->getModelObject(), $modelJoin->alias());
            }
        }
    }

    private function isAlreadyJoined(ModelJoin $modelJoin): bool
    {
        return Arrays::any($this->joinedModels, ModelJoin::equalsPredicate($modelJoin));
    }

    public function with(Relation|string $relationSelector): static
    {
        if (!BatchLoadingSession::isAllocated()) {
            $relations = ModelQueryBuilderHelper::extractRelations($this->model, $relationSelector, $this->joinedModels);
            $field = '';

            foreach ($relations as $relation) {
                $nestedField = $field ? "{$field}->{$relation->getName()}" : $relation->getName();
                $this->addRelationToFetch(new RelationToFetch($field, $relation, $nestedField));
                $field = $nestedField;
            }
        }
        return $this;
    }

    private function addRelationToFetch(RelationToFetch $relationToFetch)
    {
        if (!$this->isAlreadyAddedToFetch($relationToFetch)) {
            $this->relationsToFetch[] = $relationToFetch;
        }
    }

    private function isAlreadyAddedToFetch(RelationToFetch $relationToFetch): bool
    {
        return Arrays::any($this->relationsToFetch, RelationToFetch::equalsPredicate($relationToFetch));
    }

    public function select(array|string $columns, int $type = PDO::FETCH_NUM): static
    {
        $this->selectModel = false;
        $this->query->selectColumns = Arrays::toArray($columns);
        $this->query->selectType = $type;
        return $this;
    }

    public function selectDistinct(array|string $columns, int $type = PDO::FETCH_NUM): static
    {
        $this->query->distinct = true;
        return $this->select($columns, $type);
    }

    public function __clone(): void
    {
        $this->query = clone $this->query;
    }

    public function copy(): static
    {
        return clone $this;
    }

    public function options(array $options): static
    {
        $this->query->options = $options;
        return $this;
    }

    public function groupBy(string|array $groupBy): static
    {
        if ($this->selectModel) {
            throw new DbException("Cannot use group by without specifying columns.\n"
                . "e.g. Model::select('column, count(*)')->groupBy('column')->fetchAll();");
        }

        $this->query->groupBy = $groupBy;
        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    /** @return ModelJoin[] */
    private function createModelJoins(Relation|string $relationSelector, array|string|null $aliases, string $type, array|string $on): array
    {
        $relations = ModelQueryBuilderHelper::extractRelations($this->model, $relationSelector, $this->joinedModels);
        $relationWithAliases = ModelQueryBuilderHelper::associateRelationsWithAliases($relations, $aliases);
        return ModelQueryBuilderHelper::createModelJoins($this->getModelAliasOrTable(), $relationWithAliases, $type, $on);
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}
