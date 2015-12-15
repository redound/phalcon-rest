<?php

namespace PhalconRest\Data\Query\Parser;

use \PhalconRest\Data\Query\Condition;
use \PhalconRest\Data\Query\Sorter;
use \PhalconRest\Data\Query\Query;

class Url
{
    const OPERATOR_IS_EQUAL = 'e';
    const OPERATOR_IS_GREATER_THAN = 'gt';
    const OPERATOR_IS_GREATER_THAN_OR_EQUAL = 'gte';
    const OPERATOR_IS_LESS_THAN = 'lt';
    const OPERATOR_IS_LESS_THAN_OR_EQUAL = 'lte';
    const OPERATOR_IS_LIKE = 'l';
    const OPERATOR_IS_NOT_EQUAL = 'ne';

    const SORT_ASCENDING = 1;
    const SORT_DESCENDING = -1;

    public function createQuery($params)
    {
        $query = new Query;

        $fields = $this->extractCommaSeparatedValues($params, 'fields');
        $offset = $this->extractInt($params, 'offset');
        $limit = $this->extractInt($params, 'limit');
        $having = $this->extractArray($params, 'having');
        $where = $this->extractArray($params, 'where');
        $or = $this->extractArray($params, 'or');
        $in = $this->extractArray($params, 'in');
        $sort = $this->extractArray($params, 'sort');

        if ($fields) {

            $query->addManyFields($fields);
        }

        if ($offset) {

            $query->setOffset($offset);
        }

        if ($limit) {

            $query->setLimit($limit);
        }

        if ($having) {

            foreach($having as $field => $value) {

                $query->addCondition(new Condition(Condition::TYPE_OR, $field, Query::OPERATOR_IS_EQUAL, $value));
            }
        }

        if ($where) {

            foreach ($where as $field => $condition) {

                foreach ($condition as $rawOperator => $value) {

                    $operator = $this->extractOperator($rawOperator);

                    if (!is_null($operator)) {

                        $query->addCondition(new Condition(Condition::TYPE_AND, $field, $operator, $value));
                    }
                }
            }
        }

        if ($or) {

            foreach($or as $where) {

                foreach($where as $field => $conditions) {

                    foreach($conditions as $rawOperator => $value) {

                        $operator = $this->extractOperator($rawOperator);

                        if (!is_null($operator)) {
                            $query->addCondition(new Condition(Condition::TYPE_OR, $field, $operator, $value));
                        }
                    }
                }
            }
        }

        if ($in) {

            foreach($in as $field => $values) {

                if (!is_array($values)) {
                    continue;
                }

                $query->addCondition(new Condition(Condition::TYPE_AND, $field, Query::OPERATOR_IS_IN, $values));
            }
        }

        if ($sort) {

            foreach($sort as $field => $rawDirection) {

                $direction = null;

                switch($rawDirection) {

                    case self::SORT_DESCENDING:
                        $direction = Sorter::DESCENDING;
                        break;
                    case self::SORT_ASCENDING:
                    default:
                        $direction = Sorter::ASCENDING;
                        break;
                }

                $query->addSorter(new Sorter($field, $direction));
            }
        }

        return $query;
    }

    private function getValue($data, $field)
    {
        return array_key_exists($field, $data) ? $data[$field] : null;
    }

    private function extractCommaSeparatedValues($data, $field)
    {
        if (!$fields = $this->getValue($data, $field)) {
            return null;
        }

        return explode(',', $fields);
    }

    private function extractInt($data, $field)
    {
        if (!$int = $this->getValue($data, $field)) {
            return null;
        }

        return (int) $int;
    }

    private function extractArray($data, $field)
    {
        if (!$result = $this->getValue($data, $field)) {
            return null;
        }

        if (!$result = json_decode($result, true)) {
            return null;
        }

        return $result;
    }

    private function operatorMap()
    {
        return [
            self::OPERATOR_IS_EQUAL => Query::OPERATOR_IS_EQUAL,
            self::OPERATOR_IS_GREATER_THAN => Query::OPERATOR_IS_GREATER_THAN,
            self::OPERATOR_IS_GREATER_THAN_OR_EQUAL => Query::OPERATOR_IS_GREATER_THAN_OR_EQUAL,
            self::OPERATOR_IS_LESS_THAN => Query::OPERATOR_IS_LESS_THAN,
            self::OPERATOR_IS_LESS_THAN_OR_EQUAL => Query::OPERATOR_IS_LESS_THAN_OR_EQUAL,
            self::OPERATOR_IS_LIKE => Query::OPERATOR_IS_LIKE,
            self::OPERATOR_IS_NOT_EQUAL => Query::OPERATOR_IS_NOT_EQUAL,
        ];
    }

    private function extractOperator($operator)
    {
        $operatorMap = $this->operatorMap();

        if (array_key_exists($operator, $operatorMap)) {
            return $operatorMap[$operator];
        }

        return null;
    }
}