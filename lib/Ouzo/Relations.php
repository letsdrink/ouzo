<?php

namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Db\BelongsToRelation;
use Ouzo\Db\HasOneRelation;
use Ouzo\Db\Relation;

class Relations
{
    private $_relations;
    private $modelClass;

    public function __construct($modelClass, array $params, $primaryKeyName)
    {
        $this->modelClass = $modelClass;
        $this->_relations = array();
        if (isset($params['hasOne'])) {
            foreach ($params['hasOne'] as $relation => $relationParams) {
                $this->addRelation(new HasOneRelation($relation, $relationParams));
            }
        }
        if (isset($params['belongsTo'])) {
            foreach ($params['belongsTo'] as $relation => $relationParams) {
                $this->addRelation(new BelongsToRelation($relation, $relationParams, $primaryKeyName));
            }
        }
    }

    /**
     * @return Relation
     */
    public function getRelation($name)
    {
        if (!isset($this->_relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} has no relation: $name");
        }
        return $this->_relations[$name];
    }


    public function addRelation($relation)
    {
        $name = $relation->getName();
        if (isset($this->_relations[$name])) {
            throw new InvalidArgumentException("{$this->modelClass} already has relation: $name");
        }
        $this->_relations[$name] = $relation;
    }
}