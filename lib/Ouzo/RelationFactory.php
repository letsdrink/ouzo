<?php
namespace Ouzo;

use Ouzo\Db\Relation;

class RelationFactory
{
    static function create($relationName, $relation, $relationParams, $primaryKeyName)
    {
        if ($relationName == 'hasOne') {
            return Relation::hasOne($relation, $relationParams, $primaryKeyName);
        }
        if ($relationName == 'belongsTo') {
            return Relation::belongsTo($relation, $relationParams, $primaryKeyName);
        }
        if ($relationName == 'hasMany') {
            return Relation::hasMany($relation, $relationParams, $primaryKeyName);
        }
    }

}