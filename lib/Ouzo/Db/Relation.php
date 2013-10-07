<?php

namespace Ouzo\Db;


use InvalidArgumentException;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class Relation
{
    protected $name;
    protected $class;
    protected $foreignKey;
    protected $referencedColumn;
    protected $allowInvalidReferences;
    protected $collection;

    protected function __construct($name, array $params)
    {
        $this->validateNotEmpty($params, 'foreignKey');
        $this->validateNotEmpty($params, 'class');

        $this->name = $name;
        $this->class = $params['class'];
        $this->allowInvalidReferences = Arrays::getValue($params, 'allowInvalidReferences', false);
        $this->referencedColumn = Arrays::getValue($params, 'referencedColumn');
        $this->foreignKey = Arrays::getValue($params, 'foreignKey');
        $this->collection = Arrays::getValue($params, 'collection');
    }

    public static function inline($params)
    {
        $destinationField = Arrays::getValue($params, 'destinationField');
        return new Relation($destinationField, $params);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    function getReferencedColumn()
    {
        return $this->referencedColumn;
    }

    function getAllowInvalidReferences()
    {
        return $this->allowInvalidReferences;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function validateNotEmpty(array $params, $parameter)
    {
        if (empty($params[$parameter])) {
            throw new InvalidArgumentException($parameter . " is required");
        }
    }

    /**
     * @return Model
     */
    public function getRelationModelObject()
    {
        $modelClass = '\Model\\' . $this->class;
        return $this->relationModelObject = $modelClass::newInstance();
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
}