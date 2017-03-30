<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\AutoloadNamespaces;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\DbException;
use Ouzo\MetaModelCache;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class Relation
{
    /** @var string */
    private $name;
    /** @var string */
    private $class;
    /** @var string */
    private $localKey;
    /** @var string */
    private $foreignKey;
    /** @var bool */
    private $collection;
    /** @var string|\Closure */
    private $condition;
    /** @var string|null */
    private $order;

    /**
     * Relation constructor.
     * @param string $name
     * @param string $class
     * @param string $localKey
     * @param string $foreignKey
     * @param bool $collection
     * @param string|\Closure $condition
     * @param string|null $order
     */
    public function __construct($name, $class, $localKey, $foreignKey, $collection, $condition = '', $order = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
        $this->collection = $collection;
        $this->condition = $condition;
        $this->order = $order;
    }

    /**
     * @param array $params {
     * @var string $class
     * @var string $localKey
     * @var string $foreignKey
     * @var bool $collection
     * @var string $destinationField
     * }
     * @return Relation
     */
    public static function inline($params)
    {
        return RelationFactory::inline($params);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return WhereClause
     */
    public function getCondition()
    {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition);
        }
        return WhereClause::create($this->condition);
    }

    /**
     * @return null|string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Model
     */
    public function getRelationModelObject()
    {
        return MetaModelCache::getMetaInstance(AutoloadNamespaces::getModelNamespace() . $this->class);
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $values
     * @return mixed|null
     * @throws DbException
     */
    public function extractValue($values)
    {
        if (!$this->collection) {
            if (count($values) > 1) {
                throw new DbException("Expected one result for {$this->name}");
            }
            return Arrays::firstOrNull($values);
        }
        return $values;
    }

    /**
     * @param string $name
     * @return Relation
     */
    public function withName($name)
    {
        return new Relation($name, $this->class, $this->localKey, $this->foreignKey, $this->collection, $this->condition);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Relation {$this->name} {$this->class} {$this->localKey} {$this->foreignKey}";
    }
}
