<?php

namespace PhalconRest\Audit;

class Manager
{
    protected $delegates;

    public function __construct()
    {
        $this->delegates = [];
    }

    public function setDelegate($name, Delegate $instance)
    {
        $this->delegates[$name] = $instance;

        return $this;
    }

    public function getDelegate($name)
    {
        if (!array_key_exists($name, $this->delegates)) {
            return false;
        }

        return $this->delegates[$name];
    }
}
