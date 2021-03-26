<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;
use Ouzo\AutoloadNamespaces;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\DbException;
use Ouzo\MetaModelCache;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class Relation
{
    public function __construct(
        private string $name,
        private string $class,
        private string $localKey,
        private string $foreignKey,
        private bool $collection,
        private Closure|string|array $condition = '',
        private array|string|null $order = null)
    {
    }

    /**
     * @param array $params {
     * @var string $class
     * @var string $localKey
     * @var string $foreignKey
     * @var bool $collection
     * @var string $destinationField
     * }
     */
    public static function inline(array $params): Relation
    {
        return RelationFactory::inline($params);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCondition(): WhereClause
    {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition);
        }
        return WhereClause::create($this->condition);
    }

    public function getOrder(): string|array|null
    {
        return $this->order;
    }

    public function getRelationModelObject(): Model
    {
        return MetaModelCache::getMetaInstance(AutoloadNamespaces::getModelNamespace() . $this->class);
    }

    public function isCollection(): bool
    {
        return $this->collection;
    }

    public function extractValue(mixed $values): mixed
    {
        if (!$this->collection) {
            if (count($values) > 1) {
                throw new DbException("Expected one result for {$this->name}");
            }
            return Arrays::firstOrNull($values);
        }
        return $values;
    }

    public function withName(string $name): Relation
    {
        return new Relation($name, $this->class, $this->localKey, $this->foreignKey, $this->collection, $this->condition);
    }

    public function __toString(): string
    {
        return "Relation {$this->name} {$this->class} {$this->localKey} {$this->foreignKey}";
    }
}
