<?php
namespace Ouzo\Db;

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

    function __construct($name, $class, $localKey, $foreignKey, $collection)
    {
        $this->name = $name;
        $this->class = $class;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
        $this->collection = $collection;
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

    function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Model
     */
    public function getRelationModelObject()
    {
        return MetaModelCache::getMetaInstance('\Model\\' . $this->class);
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
        return new Relation($name, $this->class, $this->localKey, $this->foreignKey, $this->collection);
    }

    function __toString()
    {
        return "Relation {$this->name} {$this->class} {$this->localKey} {$this->foreignKey}";
    }
}