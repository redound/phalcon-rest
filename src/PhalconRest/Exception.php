<?php

namespace PhalconRest;

class Exception extends \Exception
{
    protected $info;

    public function __construct($code, $message = null, $info = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }
}
