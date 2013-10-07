<?php
namespace Ouzo;

use Ouzo\Db\BelongsToRelation;
use Ouzo\Db\HasManyRelation;
use Ouzo\Db\HasOneRelation;

class RelationFactory
{
    static function create($relationName, $relation, $relationParams, $primaryKeyName)
    {
        if ($relationName == 'hasOne') {
            return new HasOneRelation($relation, $relationParams, $primaryKeyName);
        }
        if ($relationName == 'belongsTo') {
            return new BelongsToRelation($relation, $relationParams, $primaryKeyName);
        }
        if ($relationName == 'hasMany') {
            return new HasManyRelation($relation, $relationParams, $primaryKeyName);
        }
    }

}