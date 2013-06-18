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
        $this->_db = $db ? $db : Db::getInstance();;
        $this->_model = $model;
        $this->_transformers = array();
    }

    /**
     * @return ModelQueryBuilder
     */
    public function where($params, $values = null)
    {
        return is_array($params) ? $this->whereArray($params) : $this->whereString($params, $values);
    }

    private function whereArray($params)
    {
        $values = array_values($params);
        $whereKeys = array();

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if (sizeof($value) == 0) {
                    $this->_returnEmpty = true;
                    return $this;
                }
                $in = implode(', ', array_fill(0, count($value), '?'));
                $whereKeys[] = $key . ' IN (' . $in . ')';
            } else {
                $whereKeys[] = $key . ' = ?';
            }
        }

        $this->_where = implode(' AND ', $whereKeys);
        $this->_whereValues = Arrays::flatten($values);
        return $this;
    }

    private function whereString($where, $whereValues)
    {
        $this->_where = $where;
        $this->_whereValues = $whereValues;
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

        $count = $this->_db->count()
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

        $result = $this->_db->select($this->_selectedColumns)
            ->from($this->_model->getTableName())
            ->join($this->_joinTable, $this->_joinKey, $this->_originalKey)
            ->where($this->_where, $this->_whereValues)
            ->order($this->_orderBy)
            ->limit($this->_limit)
            ->offset($this->_offset)
            ->fetchAll();
        if ($this->_selectedColumns) {
            return $result;
        } else {
            return $this->_transform($this->_model->convert($result));
        }
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
        $sql = 'DELETE FROM ' . $this->_model->getTableName() . ' WHERE ' . $this->_where;
        $this->_db->query($sql, $this->_whereValues);
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

}