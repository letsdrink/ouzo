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
    private array $relations;

    /** @var string[] */
    private static array $relationNames = ['hasOne', 'belongsTo', 'hasMany'];

    public function __construct(private string $modelClass, array $params, string $primaryKeyName)
    {
        $this->relations = [];

        $this->addRelations($params, $primaryKeyName);
    }

    public function getRelation(string $name): Relation
    {
        if (!isset($this->relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} has no relation: {$name}");
        }
        return $this->relations[$name];
    }

    public function hasRelation(string $name): bool
    {
        return isset($this->relations[$name]);
    }

    private function addRelation(Relation $relation): void
    {
        $name = $relation->getName();
        if (isset($this->relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} already has a relation: {$name}");
        }
        $this->relations[$name] = $relation;
    }

    private function addRelations(array $params, string $primaryKeyName): void
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
