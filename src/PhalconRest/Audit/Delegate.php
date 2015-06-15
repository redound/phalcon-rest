<?php

namespace PhalconRest\Audit;

class Delegate
{
    public function verify($eventname, $data)
    {
        $event = new Event;

        if (method_exists($this, $eventname)) {

            call_user_method_array($eventname, $this, [$event, $data]);
        }

        return $event;
    }
}
