<?php

namespace PhalconRest\Data\Query\Parser;

use \PhalconRest\Data\Query\Query;
use \PhalconRest\Data\Query\Sorter;
use \PhalconRest\Data\Query\Condition;

class Phql extends \Phalcon\Mvc\User\Plugin
{
    const OPERATOR_IS_EQUAL = '=';
    const OPERATOR_IS_GREATER_THAN = '>';
    const OPERATOR_IS_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_IS_IN = 'IN';
    const OPERATOR_IS_LESS_THAN = '<';
    const OPERATOR_IS_LESS_THAN_OR_EQUAL = '<=';
    const OPERATOR_IS_LIKE = 'LIKE';
    const OPERATOR_IS_NOT_EQUAL = '!=';

    const DEFAULT_KEY = 'value';

    /**
     * @param Query $query
     *
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function fromQuery(Query $query)
    {
        $modelsManager = $this->di->getShared('modelsManager');

        /** @var \Phalcon\Mvc\Model\Query\Builder $builder */
        $builder = $modelsManager->createBuilder();

        if ($query->hasModel()) {

            $model = ltrim($query->getModel(), '\\');
            $builder->from($model);
        }

        if ($query->hasOffset()) {

            $builder->offset($query->getOffset());
        }

        if ($query->hasLimit()) {

            $builder->limit($query->getLimit());
        }

        if ($query->hasConditions()) {

            $conditions = $query->getConditions();
            $firstWhere = true;

            /** @var Condition $condition */
            foreach($conditions as $conditionIndex => $condition) {

                $operator = $this->getOperator($condition->getOperator());
                $parsedValues = $this->parseValues($operator, $condition->getValue());

                $format = $this->getConditionFormat($operator);
                $valuesReplacementString = $this->getValuesReplacementString($parsedValues, $conditionIndex);
                $conditionString = sprintf($format, $condition->getField(), $operator, $valuesReplacementString);

                $bindValues = $this->getBindValues($parsedValues, $conditionIndex);

                if ($firstWhere) {
                    $builder->where($conditionString, $bindValues);
                    $firstWhere = false;
                    continue;
                }

                switch($condition->getType()) {

                    case Condition::TYPE_OR:
                        $builder->orWhere($conditionString, $bindValues);
                        break;
                    case Condition::TYPE_AND:
                    default:
                        $builder->andWhere($conditionString, $bindValues);
                        break;
                }
            }
        }

        if ($query->hasSorters()) {

            $sorters = $query->getSorters();

            /** @var Sorter $sorter */
            foreach($sorters as $sorter) {

                switch($sorter->getDirection()) {

                    case Sorter::DESCENDING:
                        $direction = 'DESC';
                        break;
                    case Sorter::ASCENDING:
                    default:
                        $direction = 'ASC';
                        break;
                }

                $builder->orderBy($sorter->getField() . ' ' . $direction);
            }
        }

        return $builder;
    }

    private function operatorMap()
    {
        return [
            Query::OPERATOR_IS_EQUAL => self::OPERATOR_IS_EQUAL,
            Query::OPERATOR_IS_GREATER_THAN => self::OPERATOR_IS_GREATER_THAN,
            Query::OPERATOR_IS_GREATER_THAN_OR_EQUAL => self::OPERATOR_IS_GREATER_THAN_OR_EQUAL,
            Query::OPERATOR_IS_IN => self::OPERATOR_IS_IN,
            Query::OPERATOR_IS_LESS_THAN => self::OPERATOR_IS_LESS_THAN,
            Query::OPERATOR_IS_LESS_THAN_OR_EQUAL => self::OPERATOR_IS_LESS_THAN_OR_EQUAL,
            Query::OPERATOR_IS_LIKE => self::OPERATOR_IS_LIKE,
            Query::OPERATOR_IS_NOT_EQUAL => self::OPERATOR_IS_NOT_EQUAL,
        ];
    }

    private function getOperator($operator)
    {
        $operatorMap = $this->operatorMap();

        if (array_key_exists($operator, $operatorMap)) {
            return $operatorMap[$operator];
        }

        return null;
    }

    private function getConditionFormat($operator)
    {
        $format = null;

        switch($operator) {

            case self::OPERATOR_IS_IN:
                $format = '%s %s (%s)';
                break;
            default:
                $format = '%s %s %s';
                break;

        }

        return $format;
    }

    private function getValuesReplacementString($values, $suffix = '')
    {
        $key = self::DEFAULT_KEY . $suffix;

        if (is_array($values)) {

            $valueIndex = 0;
            $formatted = [];

            for ($valueIndex = 0; $valueIndex < count($values); $valueIndex++) {

                $formatted[] = ':' . $key . '_' . $valueIndex . ':';
            }

            return implode(', ', $formatted);
        }

        return ':' . $key . ':';
    }

    private function getBindValues($values, $suffix = '')
    {
        $key = self::DEFAULT_KEY . $suffix;

        if (is_array($values)) {

            $valueIndex = 0;
            $parsed = [];

            foreach ($values as $value) {

                $parsed[$key . '_' . $valueIndex] = $value;
                $valueIndex++;
            }

            return $parsed;
        }

        return [$key => $values];
    }

    private function parseValues($operator, $values)
    {
        $self = $this;

        if (is_array($values)) {

            return array_map(function($value) use ($self, $operator) {
                return $self->parseValue($operator, $value);
            }, $values);
        }

        return $this->parseValue($operator, $values);
    }

    private function parseValue($operator, $value)
    {
        $parsed = null;

        switch($operator) {

            case self::OPERATOR_IS_LIKE:
                $parsed = '%' . $value . '%';
                break;
            default:
                $parsed = $value;
                break;
        }

        return $parsed;
    }
}