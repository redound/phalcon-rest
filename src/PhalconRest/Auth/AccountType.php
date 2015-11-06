<?php

namespace PhalconRest\Auth;

interface AccountType
{
    /**
     * @param array $data Login data
     *
     * @return mixed Identity model
     */
    public function login($data);

    /**
     * @param mixed $identity Identity model
     *
     * @return bool Authentication successful
     */
    public function authenticate($identity);
}
