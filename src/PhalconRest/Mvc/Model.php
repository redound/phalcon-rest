<?php

namespace PhalconRest\Mvc;

class Model extends \Phalcon\Mvc\Model
{
    public function assign(array $data, $dataColumnMap = null, $whiteList = null)
    {
        return parent::assign($data, $dataColumnMap, $whiteList === null ? $this->whitelist() : $whiteList);
    }

    public function whitelist()
    {
        return null;
    }
}