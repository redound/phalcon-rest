<?php

namespace PhalconRest\Api;

class Resource
{
    protected $key;
    protected $model;
    protected $transformer;

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }


    public function getModel()
    {
        return $this->model;
    }

    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    public function getTransformer()
    {
        return $this->transformer;
    }
}