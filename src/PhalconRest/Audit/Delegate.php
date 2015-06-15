<?php

namespace PhalconRest\Audit;

class Delegate
{
    public function verify($name, $method, $data)
    {
        $event = new Event;

        if (method_exists($this, $event)) {

            call_user_method_array($method, $this, [$event, $data]);
        }

        return $event;
    }
}
