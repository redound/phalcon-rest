<?php

namespace PhalconRest\Http;

class Request extends \Phalcon\Http\Request
{
    public function getUsername()
    {
        return $this->getServer('PHP_AUTH_USER');
    }

    public function getPassword()
    {
        return $this->getServer('PHP_AUTH_PW');
    }

    public function getToken()
    {
        $authHeader = $this->getHeader('AUTHORIZATION');
        $authQuery = $this->getQuery('token');

        return ($authQuery ? $authQuery : $this->parseBearerValue($authHeader));
    }

    protected function parseBearerValue($string)
    {
        if(strpos(trim($string), 'Bearer') !== 0){
            return null;
        }

        return preg_replace('/.*\s/', '', $string);
    }
}
