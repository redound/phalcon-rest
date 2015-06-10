<?php

namespace PhalconRest\Exceptions;

class Exception extends \Exception
{
    public function __construct($code, $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
