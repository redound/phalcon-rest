<?php

namespace PhalconRest\Data;

use PhalconRest\Data\Query\Condition;
use PhalconRest\Data\Query\Sorter;

class Query
{
    const OPERATOR_IS_EQUAL = 0;
    const OPERATOR_IS_GREATER_THAN = 1;
    const OPERATOR_IS_GREATER_THAN_OR_EQUAL = 2;
    const OPERATOR_IS_IN = 3;
    const OPERATOR_IS_LESS_THAN = 4;
    const OPERATOR_IS_LESS_THAN_OR_EQUAL = 5;
    const OPERATOR_IS_LIKE = 6;
    const OPERATOR_IS_NOT_EQUAL = 7;
    const OPERATOR_CONTAINS = 8;
    const OPERATOR_NOT_CONTAINS = 9;

    protected $offset = null;
    protected $limit = null;
    protected $fields = [];
    protected $conditions = [];
    protected $sorters = [];
    protected $excludes = [];

    public function __construct()
    {

    }

    public function addField($field)
    {
        $this->fields[] = $field;
        return $this;
    }

    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function addSorter(Sorter $sorter)
    {
        $this->sorters[] = $sorter;
        return $this;
    }

    public function merge(Query $query)
    {
        if ($query->hasFields()) {
            $this->addManyFields($query->getFields());
        }

        if ($query->hasOffset()) {
            $this->setOffset($query->getOffset());
        }

        if ($query->hasLimit()) {
            $this->setLimit($query->getLimit());
        }

        if ($query->hasConditions()) {
            $this->addManyConditions($query->getConditions());
        }

        if ($query->hasSorters()) {
            $this->addManySorters($query->getSorters());
        }

        if ($query->hasExcludes()) {
            $this->addManyExcludes($query->getExcludes());
        }

        return $this;
    }

    public function hasFields()
    {
        return !empty($this->fields);
    }

    public function addManyFields($fields)
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function hasOffset()
    {
        return !is_null($this->offset);
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function hasLimit()
    {
        return !is_null($this->limit);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function hasConditions()
    {
        return !empty($this->conditions);
    }

    public function addManyConditions($conditions)
    {
        $this->conditions = array_merge($this->conditions, $conditions);
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function hasSorters()
    {
        return !empty($this->sorters);
    }

    public function addManySorters($sorters)
    {
        $this->sorters = array_merge($this->sorters, $sorters);
        return $this;
    }

    public function getSorters()
    {
        return $this->sorters;
    }

    public function hasExcludes()
    {
        return !empty($this->excludes);
    }

    public function addManyExcludes($excludes)
    {
        $this->excludes = array_merge($this->excludes, $excludes);
        return $this;
    }

    public function getExcludes()
    {
        return $this->excludes;
    }
}
