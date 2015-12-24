<?php

namespace PhalconRest\Http;

class Request extends \Phalcon\Http\Request
{
    /**
     * Returns auth username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->getServer('PHP_AUTH_USER');
    }

    /**
     * Returns auth password
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->getServer('PHP_AUTH_PW');
    }

    /**
     * Returns the data posted by the client. By default this method returns POST data.
     * Override this method yo provide data from another source, e.g. JSON from the body
     *
     * @return mixed
     */
    public function getPostedData()
    {
        return $this->getPost();
    }

    /**
     * Returns token from the request.
     * Uses token URL query field, or Authorization header
     *
     * @return string|null
     */
    public function getToken()
    {
        $authHeader = $this->getHeader('AUTHORIZATION');
        $authQuery = $this->getQuery('token');

        return ($authQuery ? $authQuery : $this->parseBearerValue($authHeader));
    }

    protected function parseBearerValue($string)
    {
        if (strpos(trim($string), 'Bearer') !== 0) {
            return null;
        }

        return preg_replace('/.*\s/', '', $string);
    }
}
