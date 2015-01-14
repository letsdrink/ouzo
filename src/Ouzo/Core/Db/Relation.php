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
    private $name;
    private $class;
    private $localKey;
    private $foreignKey;
    private $collection;
    private $condition;

    public function __construct($name, $class, $localKey, $foreignKey, $collection, $condition = '')
    {
        $this->name = $name;
        $this->class = $class;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
        $this->collection = $collection;
        $this->condition = $condition;
    }

    public static function inline($params)
    {
        return RelationFactory::inline($params);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getLocalKey()
    {
        return $this->localKey;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCondition()
    {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition);
        }
        return WhereClause::create($this->condition);
    }

    /**
     * @return Model
     */
    public function getRelationModelObject()
    {
        return MetaModelCache::getMetaInstance(AutoloadNamespaces::getModelNamespace() . $this->class);
    }

    public function isCollection()
    {
        return $this->collection;
    }

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

    public function withName($name)
    {
        return new Relation($name, $this->class, $this->localKey, $this->foreignKey, $this->collection, $this->condition);
    }

    public function __toString()
    {
        return "Relation {$this->name} {$this->class} {$this->localKey} {$this->foreignKey}";
    }
}
