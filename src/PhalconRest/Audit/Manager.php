<?php

namespace PhalconRest\Audit;

class Manager
{
    protected $delegates;

    public function __construct()
    {
        $this->delegates = [];
    }

    public function mount($name, $instance)
    {
        return $this->setDelegate($name, $instance);
    }

    protected function setDelegate($name, Delegate $instance)
    {
        $this->delegates[$name] = $instance;

        return $this;
    }

    protected function getDelegate($name)
    {
        if (!array_key_exists($name, $this->delegates)) {
            return false;
        }

        return $this->delegates[$name];
    }

    public function check($name, $event, $data)
    {
        $check = new Event;

        if ($delegate = $this->getDelegate($name)) {

            if (!method_exists($delegate, $event)) {

                return $check;
            }

            call_user_method_array($event, $delegate, [$check, $data]);
        }

        return $check;
    }
}
