<?php

namespace PhalconRest;

class Exception extends \Exception
{
    protected $developerInfo;
    protected $userInfo;

    public function __construct($code, $message = null, $developerInfo = null, $userInfo = null)
    {
        parent::__construct($message, $code);

        $this->developerInfo = $developerInfo;
        $this->userInfo = $userInfo;
    }

    public function getDeveloperInfo()
    {
        return $this->developerInfo;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }
}
