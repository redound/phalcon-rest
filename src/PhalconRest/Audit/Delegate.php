<?php

namespace PhalconRest\Audit;

trait Delegate
{
    public function verify($eventname, $data)
    {
        $args = func_get_args();
        $args[0] = new Event;

        if (method_exists($this, $eventname)) {

            call_user_method_array($eventname, $this, $args);
        }

        return $args[0];
    }
}
