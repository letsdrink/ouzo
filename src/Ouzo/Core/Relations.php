<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationFactory;

class Relations
{
    /** @var array */
    private $relations;
    /** @var string */
    private $modelClass;

    /** @var array */
    private static $relationNames = ['hasOne', 'belongsTo', 'hasMany'];

    /**
     * @param string $modelClass
     * @param array $params
     * @param string $primaryKeyName
     */
    public function __construct($modelClass, array $params, $primaryKeyName)
    {
        $this->modelClass = $modelClass;
        $this->relations = [];

        $this->addRelations($params, $primaryKeyName);
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException
     * @return Relation
     */
    public function getRelation($name)
    {
        if (!isset($this->relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} has no relation: $name");
        }
        return $this->relations[$name];
    }

    /***
     * @param string $name
     * @return bool
     */
    public function hasRelation($name)
    {
        return isset($this->relations[$name]);
    }

    /**
     * @param Relation $relation
     * @return void
     */
    private function addRelation(Relation $relation)
    {
        $name = $relation->getName();
        if (isset($this->relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} already has a relation: $name");
        }
        $this->relations[$name] = $relation;
    }

    /**
     * @param array $params
     * @param string $primaryKeyName
     * @return void
     */
    private function addRelations(array $params, $primaryKeyName)
    {
        foreach (self::$relationNames as $relationName) {
            if (isset($params[$relationName])) {
                foreach ($params[$relationName] as $relation => $relationParams) {
                    $this->addRelation(RelationFactory::create($relationName, $relation, $relationParams, $primaryKeyName, $this->modelClass));
                }
            }
        }
    }
}
