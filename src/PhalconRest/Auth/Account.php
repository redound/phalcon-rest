<?php

namespace PhalconRest\Auth;

interface Account 
{

	public function login($username = null, $password = null);
}