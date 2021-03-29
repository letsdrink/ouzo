<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Ouzo\Db\BatchLoadingSession;
use Ouzo\Db\ModelDefinition;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationFetcher;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Exception\ValidationException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use PDO;
use ReflectionClass;
use Serializable;

class Model extends Validatable implements Serializable, JsonSerializable
{
    private ModelDefinition $modelDefinition;
    private array $attributes;
    private array $modifiedFields;

    /**
     * Creates a new model object.
     * Accepted parameters:
     * @param array $params {
     * @var string $table defaults to pluralized class name. E.g. customer_orders for CustomerOrder
     * @var string $primaryKey defaults to id
     * @var string $sequence defaults to table_primaryKey_seq
     * @var string $hasMany specification of a has-many relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $hasOne specification of a has-one relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $belongsTo specification of a belongs-to relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $fields mapped column names
     * @var string $attributes array of column => value
     * @var string $beforeSave function to invoke before insert or update
     * @var string $afterSave function to invoke after insert or update
     * }
     */
    public function __construct(array $params)
    {
        $this->prepareParameters($params);

        $this->modelDefinition = ModelDefinition::get(get_called_class(), $params);
        $primaryKeyName = $this->modelDefinition->primaryKey;
        $attributes = $this->modelDefinition->mergeWithDefaults($params['attributes'], $params['fields']);

        if (isset($attributes[$primaryKeyName]) && Strings::isBlank($attributes[$primaryKeyName])) {
            unset($attributes[$primaryKeyName]);
        }
        $this->attributes = $this->filterAttributes($attributes);
        $this->modifiedFields = array_keys($this->attributes);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->modifiedFields[] = $name;
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        if (empty($name)) {
            throw new Exception('Illegal attribute: field name for Model cannot be empty');
        }
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if ($this->hasRelation($name)) {
            $this->fetchRelation($name);
            return $this->attributes[$name];
        }
        return null;
    }

    public function __isset(string $name): bool
    {
        return $this->__get($name) !== null;
    }

    public function __unset(string $name): void
    {
        unset($this->attributes[$name]);
    }

    public function assignAttributes(array $attributes): void
    {
        unset($attributes[$this->modelDefinition->primaryKey]);
        $this->modifiedFields = array_merge($this->modifiedFields, array_keys($attributes));
        $this->attributes = array_merge($this->attributes, $this->filterAttributesPreserveNull($attributes));
    }

    private function filterAttributesPreserveNull(array $data): array
    {
        return array_intersect_key($data, array_flip($this->modelDefinition->fields));
    }

    private function filterAttributes(array $data): array
    {
        return Arrays::filter($this->filterAttributesPreserveNull($data), Functions::notNull());
    }

    public function attributes(): array
    {
        return array_replace(array_fill_keys($this->modelDefinition->fields, null), $this->attributes);
    }

    public function definedAttributes(): array
    {
        return $this->filterAttributesPreserveNull($this->attributes());
    }

    private function prepareParameters(array &$params): void
    {
        if (empty($params['attributes'])) {
            $params['attributes'] = [];
        }
        if (empty($params['fields'])) {
            throw new InvalidArgumentException('Fields are required');
        }
    }

    public function getTableName(): string
    {
        return $this->modelDefinition->table;
    }

    public function insert(): ?int
    {
        return $this->doInsert(function ($attributes) {
            return Query::insert($attributes)->into($this->modelDefinition->table);
        });
    }

    public function insertOrDoNothing(): ?int
    {
        return $this->doInsert(fn($attributes) => Query::insertOrDoNoting($attributes)->into($this->modelDefinition->table));
    }

    public function upsert(array $upsertConflictColumns = []): ?int
    {
        return $this->doInsert(function ($attributes) use ($upsertConflictColumns) {
            if (empty($upsertConflictColumns)) {
                $upsertConflictColumns = [$this->getIdName()];
            }
            return Query::upsert($attributes)->onConflict($upsertConflictColumns)->table($this->modelDefinition->table);
        });
    }

    private function doInsert($callback): ?int
    {
        $this->callBeforeSaveCallbacks();

        $primaryKey = $this->modelDefinition->primaryKey;
        $attributes = $this->filterAttributesPreserveNull($this->attributes);

        $query = $callback($attributes);

        $sequence = $primaryKey && $this->$primaryKey !== null ? null : $this->modelDefinition->sequence;
        $lastInsertedId = QueryExecutor::prepare($this->modelDefinition->db, $query)->insert($sequence);

        if ($primaryKey) {
            if ($sequence) {
                $this->$primaryKey = $lastInsertedId;
            } else {
                $lastInsertedId = $this->$primaryKey;
            }
        }

        $this->callAfterSaveCallbacks();
        $this->resetModifiedFields();

        return $lastInsertedId;
    }

    public function callAfterSaveCallbacks(): void
    {
        $this->callCallbacks($this->modelDefinition->afterSaveCallbacks);
    }

    public function update(): void
    {
        $this->callBeforeSaveCallbacks();

        $attributes = $this->getAttributesForUpdate();
        if ($attributes) {
            $query = Query::update($attributes)
                ->table($this->modelDefinition->table)
                ->where([$this->modelDefinition->primaryKey => $this->getId()]);

            QueryExecutor::prepare($this->modelDefinition->db, $query)->execute();
        }

        $this->callAfterSaveCallbacks();
        $this->resetModifiedFields();
    }

    public function callBeforeSaveCallbacks(): void
    {
        $this->callCallbacks($this->modelDefinition->beforeSaveCallbacks);
    }

    private function callCallbacks($callbacks): void
    {
        foreach ($callbacks as $callback) {
            if (is_string($callback)) {
                $callback = [$this, $callback];
            }
            call_user_func($callback, $this);
        }
    }

    public function insertOrUpdate(): void
    {
        $this->isNew() ? $this->insert() : $this->update();
    }

    public function isNew(): bool
    {
        return is_null($this->getId());
    }

    public function updateAttributes(array $attributes)
    {
        if (!$this->updateAttributesIfValid($attributes)) {
            throw new ValidationException($this->getErrorObjects());
        }
    }

    public function updateAttributesIfValid(array $attributes): bool
    {
        $this->assignAttributes($attributes);
        if ($this->isValid()) {
            $this->update();
            return true;
        }
        return false;
    }

    public function delete(): bool
    {
        return (bool)$this->where([$this->modelDefinition->primaryKey => $this->getId()])->deleteAll();
    }

    public function getId(): ?int
    {
        $primaryKeyName = $this->modelDefinition->primaryKey;
        return $this->$primaryKeyName;
    }

    public function getIdName(): string
    {
        return $this->modelDefinition->primaryKey;
    }

    public function getSequenceName(): string
    {
        return $this->modelDefinition->sequence;
    }

    /**
     * Returns model object as a nicely formatted string.
     */
    public function inspect(): string
    {
        return get_called_class() . Objects::toString(Arrays::filter($this->attributes, Functions::notNull()));
    }

    public function getModelName(): string
    {
        $function = new ReflectionClass($this);
        return $function->getShortName();
    }

    public static function getFields(): array
    {
        return static::metaInstance()->_getFields();
    }

    public function _getFields(): array
    {
        return $this->modelDefinition->fields;
    }

    public static function getFieldsWithoutPrimaryKey(): array
    {
        return static::metaInstance()->_getFieldsWithoutPrimaryKey();
    }

    private function _getFieldsWithoutPrimaryKey(): array
    {
        return array_diff($this->modelDefinition->fields, [$this->modelDefinition->primaryKey]);
    }

    private function fetchRelation(string $name): void
    {
        $relation = $this->getRelation($name);
        $relationFetcher = new RelationFetcher($relation);
        $results = BatchLoadingSession::getBatch($this);
        $relationFetcher->transform($results);
    }

    public static function newInstance(array $attributes): static
    {
        $className = get_called_class();
        /** @var Model $object */
        $object = new $className($attributes);
        $object->resetModifiedFields();
        return $object;
    }

    public static function metaInstance(): static
    {
        return MetaModelCache::getMetaInstance(get_called_class());
    }

    /**
     * @return static[]
     */
    public static function all(): array
    {
        return static::queryBuilder()->fetchAll();
    }

    public static function select(array|string $columns, int $type = PDO::FETCH_NUM): ModelQueryBuilder
    {
        return static::queryBuilder()->select($columns, $type);
    }

    public static function selectDistinct(array|string $columns, int $type = PDO::FETCH_NUM): ModelQueryBuilder
    {
        return static::queryBuilder()->selectDistinct($columns, $type);
    }

    public static function join(Relation|string $relation, null|array|string $alias = null, string $type = 'LEFT', array|string $on = []): ModelQueryBuilder
    {
        return static::queryBuilder()->join($relation, $alias, $type, $on);
    }

    public static function innerJoin(Relation|string $relation, ?string $alias = null, array|string $on = []): ModelQueryBuilder
    {
        return static::queryBuilder()->innerJoin($relation, $alias, $on);
    }

    public static function rightJoin(Relation|string $relation, ?string $alias = null, array|string $on = []): ModelQueryBuilder
    {
        return static::queryBuilder()->rightJoin($relation, $alias, $on);
    }

    public static function using(string $relation, ?string $alias = null): ModelQueryBuilder
    {
        return static::queryBuilder()->using($relation, $alias);
    }

    public static function where(null|string|array|WhereClause $params = '', null|string|array $values = []): ModelQueryBuilder
    {
        return static::queryBuilder()->where($params, $values);
    }

    public static function queryBuilder(?string $alias = null): ModelQueryBuilder
    {
        $obj = static::metaInstance();
        return new ModelQueryBuilder($obj, $obj->modelDefinition->db, $alias);
    }

    public static function count(string|array $where = '', ?array $bindValues = null): int
    {
        return static::metaInstance()->where($where, $bindValues)->count();
    }

    public static function alias(string $alias): ModelQueryBuilder
    {
        return static::queryBuilder($alias);
    }

    /**
     * @return static[]
     */
    public static function find(array|string $where, array $whereValues, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        return static::metaInstance()
            ->where($where, $whereValues)
            ->order($orderBy)
            ->limit($limit)
            ->offset($offset)
            ->fetchAll();
    }

    /**
     * Executes a native sql and returns an array of model objects created by passing every result row to the model constructor.
     * @return static[]
     */
    public static function findBySql(string $nativeSql, null|string|array $params = []): array
    {
        $meta = static::metaInstance();
        $results = $meta->modelDefinition->db->query($nativeSql, Arrays::toArray($params))->fetchAll();

        return Arrays::map($results, fn($row) => $meta->newInstance($row));
    }

    public static function findById(?int $value): static
    {
        return static::metaInstance()->internalFindById($value);
    }

    private function internalFindById(?int $value): static
    {
        if (!$this->modelDefinition->primaryKey) {
            throw new DbException("Primary key is not defined for table {$this->modelDefinition->table}");
        }
        $result = $this->internalFindByIdOrNull($value);
        if ($result) {
            return $result;
        }
        throw new DbException("{$this->modelDefinition->table} with {$this->modelDefinition->primaryKey}={$value} not found");
    }

    public static function findByIdOrNull(?int $value): ?static
    {
        return static::metaInstance()->internalFindByIdOrNull($value);
    }

    private function internalFindByIdOrNull(?int $value): ?static
    {
        return $this->where([$this->modelDefinition->primaryKey => $value])->fetch();
    }

    public static function create(array $attributes = []): static
    {
        $instance = static::newInstance($attributes);
        if ($instance->isValid()) {
            $instance->insert();
            return $instance;
        }
        throw new ValidationException($instance->getErrorObjects());
    }

    public static function createOrUpdate(array $attributes = [], array $upsertConflictColumns = []): static
    {
        $instance = static::newInstance($attributes);
        if ($instance->isValid()) {
            $instance->upsert($upsertConflictColumns);
            return $instance;
        }
        throw new ValidationException($instance->getErrorObjects());
    }

    public static function createWithoutValidation(array $attributes = []): static
    {
        $instance = static::newInstance($attributes);
        $instance->insert();
        return $instance;
    }

    public function reload(): static
    {
        $this->attributes = $this->findById($this->getId())->attributes;
        $this->resetModifiedFields();
        return $this;
    }

    public function nullifyIfEmpty(string...$fields): void
    {
        foreach ($fields as $field) {
            if (isset($this->$field) && !is_bool($this->$field) && Strings::isBlank($this->$field)) {
                $this->$field = null;
            }
        }
    }

    public function get(string $names, mixed $default = null): mixed
    {
        return Objects::getValueRecursively($this, $names, $default);
    }

    public function hasRelation(string $name): bool
    {
        return $this->modelDefinition->relations->hasRelation($name);
    }

    public function getRelation(string $name): Relation
    {
        return $this->modelDefinition->relations->getRelation($name);
    }

    public function __toString(): string
    {
        return $this->inspect();
    }

    public function resetModifiedFields(): void
    {
        $this->modifiedFields = [];
    }

    private function getAttributesForUpdate(): array
    {
        $attributes = $this->filterAttributesPreserveNull($this->attributes);
        return array_intersect_key($attributes, array_flip($this->modifiedFields));
    }

    public function serialize(): string
    {
        return serialize($this->attributes);
    }

    public function unserialize($serialized): void
    {
        $result = unserialize($serialized);
        foreach ($result as $key => $value) {
            $this->$key = $value;
        }
    }

    public function jsonSerialize(): string
    {
        return json_encode($this->attributes);
    }
}
