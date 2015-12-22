<?php

namespace PhalconRest;

class Bootstrap
{
    public function __construct(...$executables)
    {
        $this->_executables = $executables;
    }

    public function run(...$args)
    {
        foreach ($this->_executables as $executable) {
            call_user_func_array([$executable, 'run'], $args);
        }
    }
}