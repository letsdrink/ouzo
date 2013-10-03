<?php

namespace Ouzo\Db;


use InvalidArgumentException;
use Ouzo\Utilities\Arrays;

class Relation
{
    protected $name;
    protected $class;
    protected $foreignKey;
    protected $referencedColumn;
    protected $allowInvalidReferences;

    function __construct($name, array $params)
    {
        $this->name = $name;
        $this->validateNotEmpty($params, 'class');
        $this->class = $params['class'];
        $this->allowInvalidReferences = Arrays::getValue($params, 'allowInvalidReferences', false);
        $this->referencedColumn = Arrays::getValue($params, 'referencedColumn');
        $this->foreignKey = Arrays::getValue($params, 'foreignKey');
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
            throw new InvalidArgumentException($parameter . "is required");
        }
    }
}