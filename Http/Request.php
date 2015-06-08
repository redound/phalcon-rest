<?php

namespace PhalconRest\Http;

class Request extends \Phalcon\Http\Request
{

	protected function extractToken($string)
	{

		return trim(str_replace('Bearer', '', $string));
	}

	public function getBearer()
	{

		$authHeader = $this->getHeader('AUTHORIZATION');
		$authHeaderEx = explode(' ', $authHeader);

		if (isset($authHeaderEx[0])) {

			return $authHeaderEx[0];
		}
	}

	public function getAuth()
	{

		$authHeader = $this->getHeader('AUTHORIZATION');
		$authHeaderEx = explode(' ', $authHeader);

		if (!isset($authHeaderEx[1])) {

			return;
		}

		$auth = base64_decode($authHeaderEx[1]);
		$authEx = explode(':', $auth);

		if (!isset($authEx[0]) && !isset($authEx[1])) {

			return;
		}

		return ['username'=>$authEx[0], 'password'=>$authEx[1]];
	}

	public function getUsername()
	{
		return $this->getAuth()['username'];
	}

	public function getPassword()
	{
		return $this->getAuth()['password'];
	}

	public function getToken()
	{

		$authHeader 		= $this->getHeader('AUTHORIZATION');
		$authQuery  		= $this->getQuery('token');

		return ($authQuery ? $authQuery : $this->extractToken($authHeader));
	}
}
