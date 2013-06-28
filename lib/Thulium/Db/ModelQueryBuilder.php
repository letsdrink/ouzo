<?php
namespace Thulium\Db;

use Thulium\Db;
use Thulium\Model;
use Thulium\Utilities\Arrays;

class ModelQueryBuilder
{
    private $_db;
    private $_model;
    private $_transformers;
    private $_query;

    public function __construct($model, $db = null)
    {
        $this->_db = $db ? $db : Db::getInstance();
        $this->_model = $model;
        $this->_transformers = array();

        $this->_query = new Query();
        $this->_query->table = $model->getTableName();
    }

    /**
     * @return ModelQueryBuilder
     */
    public function where($params = '', $values = null)
    {
        $this->_query->where = $params;
        $this->_query->whereValues = $values;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function order($columns)
    {
        $this->_query->order = $columns;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function offset($offset)
    {
        $this->_query->offset = $offset;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function limit($limit)
    {
        $this->_query->limit = $limit;
        return $this;
    }

    public function count()
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->count();
    }

    /**
     * @return Model
     */
    public function fetch()
    {
        $object = $this->fetchAll();
        return Arrays::firstOrNull($object);
    }

    /**
     * @return Model[]
     */
    public function fetchAll()
    {
        $result = QueryExecutor::prepare($this->_db, $this->_query)->fetchAll();
        return $this->_query->selectColumns ? $result : $this->_transform($this->_model->convert($result));
    }

    private function _transform($results)
    {
        foreach ($this->_transformers as $transformer) {
            $transformer->transform($results);
        }
        return $results;
    }

    public function deleteAll()
    {
        return QueryExecutor::prepare($this->_db, $this->_query)->delete();
    }

    /**
     * @return ModelQueryBuilder
     */
    public function join($joinModel, $joinKey, $originalKey = null)
    {
        $model = Model::newInstance('\Model\\' . $joinModel);
        $this->_query->joinTable = $model->getTableName();
        $this->_query->joinKey = $joinKey;
        $this->_query->idName = $originalKey ? $originalKey : $this->_model->getIdName();
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function with($relation, $foreignKey, $destinationField, $referencedColumn = null, $allowMissing = false)
    {
        $this->_transformers[] = new RelationFetcher($relation, $foreignKey, $destinationField, $referencedColumn, $allowMissing);
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function select($columns)
    {
        $this->_query->selectColumns = is_array($columns) ? $columns : array($columns);
        return $this;
    }

}