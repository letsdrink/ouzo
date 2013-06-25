<?php
namespace Thulium\Db;

use Thulium\Db;
use Thulium\Model;
use Thulium\Utilities\Arrays;

class ModelQueryBuilder
{
    private $_db;
    private $_model;
    private $_where;
    private $_whereValues;
    private $_orderBy;
    private $_offset;
    private $_limit;
    private $_returnEmpty;
    private $_joinTable;
    private $_joinKey;
    private $_originalKey;
    private $_transformers;
    private $_selectedColumns = array();

    public function __construct($model, $db = null)
    {
        $this->_db = $db ? $db : Db::getInstance();
        $this->_model = $model;
        $this->_transformers = array();
    }

    /**
     * @return ModelQueryBuilder
     */
    public function where($params, $values = null)
    {
        $this->_setReturnEmptyIfNeeded($params);
        $this->_where = $params;
        $this->_whereValues = $values;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function order($columns)
    {
        $this->_orderBy = $columns;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * @return ModelQueryBuilder
     */
    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    public function count()
    {
        if ($this->_returnEmpty) {
            return 0;
        }

        $count = $this->queryBuilderCount()
            ->from($this->_model->getTableName())
            ->join($this->_joinTable, $this->_joinKey, $this->_originalKey)
            ->where($this->_where, $this->_whereValues)
            ->fetch();
        return $count[0];
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
        if ($this->_returnEmpty) {
            return array();
        }

        $result = $this->queryBuilderSelect($this->_selectedColumns)
            ->from($this->_model->getTableName())
            ->join($this->_joinTable, $this->_joinKey, $this->_originalKey)
            ->where($this->_where, $this->_whereValues)
            ->order($this->_orderBy)
            ->limit($this->_limit)
            ->offset($this->_offset)
            ->fetchAll();
        return $this->_selectedColumns ? $result : $this->_transform($this->_model->convert($result));
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
        if ($this->_returnEmpty) {
            return 0;
        }

        $queryBuilder = $this->queryBuilderDelete();
        $queryBuilder->from($this->_model->getTableName())
            ->where($this->_where, $this->_whereValues)
            ->delete();
        return $this->rowAffected();
    }

    public function rowAffected()
    {
        return $this->_db->query->rowCount();
    }

    /**
     * @return ModelQueryBuilder
     */
    public function join($joinModel, $joinKey, $originalKey = null)
    {
        $model = Model::newInstance('\Model\\' . $joinModel);
        $this->_joinTable = $model->getTableName();
        $this->_joinKey = $joinKey;
        $this->_originalKey = $originalKey ? $originalKey : $this->_model->getIdName();
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
        $this->_selectedColumns = is_array($columns) ? $columns : array($columns);
        return $this;
    }

    private function _setReturnEmptyIfNeeded($params)
    {
        if (is_array($params)) {
            foreach ($params as $value) {
                if (is_array($value) && sizeof($value) == 0) {
                    $this->_returnEmpty = true;
                    break;
                }
            }
        }
    }

    private function queryBuilderSelect(array $columns = array())
    {
        return new QueryBuilder($this->_db, $columns);
    }

    private function queryBuilderCount()
    {
        return $this->queryBuilderSelect(array('count(*)'));
    }

    public function queryBuilderDelete()
    {
        return new QueryBuilder($this->_db, array(), true);
    }

}