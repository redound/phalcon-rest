<?php

namespace PhalconRest\Data\Query;

class Condition
{
    const TYPE_AND = 0;
    const TYPE_OR = 1;

    public $type;
    public $field;
    public $operator;
    public $value;

    public function __construct($type, $field, $operator, $value)
    {
        $this->type = $type;
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue()
    {
        return $this->value;
    }
}